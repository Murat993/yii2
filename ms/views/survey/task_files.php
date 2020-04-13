<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use \common\models\Task;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TaskFile */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Файлы задания');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Анкеты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Задания'), 'url' => ['view', 'id' => $id, 'ms_survey_id' => $ms_survey_id, 'tab' => 2]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Файлы задания');
?>
<div class="task-file-index">
    <style>
        .employee-image-index img {
            display: inline-block;
            width: 250px;
            height: 200px;
            overflow: hidden;
            border: 5px solid #FFFFFF;
            background: #FFFFFF;
            outline: 1px solid #CCCCCC;
            margin: 10px;
        }
    </style>
    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['style' => 'width:80%'],
//        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'width:2%'],
            ],
            [
                'attribute' => 'file_name',
                'headerOptions' => ['style' => 'width:20%'],
                'format' => 'raw',
                'value' => function($data){
                    if($data->type == Task::PHOTO){
                        return Html::a(
                                Html::img(Url::toRoute('/uploads/task-answer/' . $data->file_name)),
                                Url::toRoute('/uploads/task-answer/' . $data->file_name),
                                ['class' => 'fancybox employee-image-index']
                        );
                    } elseif ($data->type == Task::AUDIO){
                        return substr($data->file_name, 11) . "<br><audio controls='controls'>
                                <source src='" . "/uploads/task-answer/". $data->file_name . "' type='audio/mp3' />
                                </audio>";
                    }
                    return '';
                },

            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Действия'),
                'headerOptions' => ['style' => 'width:15%'],
                'template' => '{download} {delete}',
                'buttons' => [
                    'delete' => function ($url, $model) use ($ms_survey_id, $id){
                        return Html::a('<span class=\'label label-danger\' >' . Yii::t('app',  'Удалить') . '</span>', [
                            'delete-task-file',
                            'id' => $model->id,
                            'task_id' => $model->task_id,
                            'file_name' => $model->file_name,
                            'ms_survey_id' => $ms_survey_id,
                            'survey_id' => $id
                        ]);
                    },
                    'download' => function ($url, $model) {
                        return Html::a(
                            '<span class=\'label label-primary\'>' . Yii::t('app',  'Скачать') . '</span>',
                            "/uploads/task-answer/". $model->file_name,
                            [
                                'download' => true,
                            ]);
                    }
                ]
            ],

        ],
    ]); ?>
</div>

<script>
    $('.fancybox').fancybox();
    $(document).ready(function () {
        $('#modalEmployeeButton').click(function () {
            $('#modal-employee').modal('show')
                .find('#modalEmployeeContent')
                .load($(this).attr('value'));
        });
    });
</script>

