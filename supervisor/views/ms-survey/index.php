<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SurveySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$class = $dataProvider->getCount() ? 'table datatable-basic' : 'table';
?>
<?= $this->render('_search', ['model' => $searchModel, 'client_id' => $client_id]) ?>
<div class="<?= $panel ?>">
    <div class="panel-heading">
        <h6 class="panel-title"><?= $header ?></h6>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'tableOptions' => [
            'class' => $class
        ],
        'columns' => [
            'id',
            ['attribute' => 'surveyFilial.survey.name', 'label' => Yii::t('app', 'Название')],
            ['attribute' => 'surveyFilial.filial.cityLabel', 'label' => Yii::t('app', 'Город')],
            ['attribute' => 'surveyFilial.filial.filialText', 'label' => Yii::t('app', 'Объект')],
            ['attribute' => 'ms.name', 'label' => Yii::t('app', 'Тайный покупатель')],
            ['label' => Yii::t('app', 'Дата визита'), 'value' => function ($model, $x) {
                return substr($model->msSurveyDateVisited->date, 0, -9);
            }],
            ['label' => Yii::t('app', 'Вопросы'), 'value' => function ($model, $x) {
                return Yii::$app->questService->getQuestionsCount($model->surveyFilial->survey->questionary_id);
            }],
            ['label' => Yii::t('app', 'Задания'), 'value' => function ($model, $x) {
                return \common\models\Task::find()->where(['survey_id' => $model->surveyFilial->survey_id])->count();
            }],
            [
                'label' => 'Процент выполнения',
                'value' => function ($data) {
                    return round($data->percent, 1) . '%';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Действия'),
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($x, $model) use ($searchModel) {
                        if ($searchModel->status == \common\models\MsSurvey::STATUS_COMPLETED) {
                            $text = Yii::t('app', 'Просмотр');
                            return Html::a("<span class='label label-success'>{$text}</span>", [
                                'view',
                                'id' => $model->id,
                                'completed' => 1
                            ]);
                        } else {
                            $text = Yii::t('app', 'Выполнить');
                            return Html::a("<span class='label label-success'>{$text}</span>", [
                                'view',
                                'id' => $model->id,
                            ]);
                        }
                    }
                ]
            ],
        ],
    ]); ?>
</div>
