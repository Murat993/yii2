<?php

use yii\grid\GridView;
use yii\helpers\Html;
use common\models\MsSurvey;


/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SurveySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$class = $dataProvider->getCount() ? 'table datatable-highlight' : 'table';
$classList = $dataProviderList->getCount() ? 'table datatable-highlight' : 'table';
$dataProvider->sort = false;
$count = $dataProvider->count;

?>
<div class="panel panel-flat" style="padding:10px">
    <?= $this->render('_search_stats', ['model' => $searchModel]) ?>
</div>
<style>
    tfoot {
        display: table-header-group;
    }
</style>
<div class="panel panel-flat">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'tableOptions' => [
            'class' => $class
        ],
        'showFooter' => true,
        'columns' => [
            [
                'attribute' => 'name',
                'label' => Yii::t('app', 'Сценарий'),
                'value' => function ($model, $key, $index, $column) use ($count) {
                    if ($index == $count - 1) {
                        return Yii::t('app', 'Всего');
                    }
                    return $model['name'];
                }
            ],
            ['attribute' => 'total', 'label' => Yii::t('app', 'Всего')],
            ['attribute' => 'naznacheno', 'label' => Yii::t('app', 'Назначено')],
            ['attribute' => 'in_process', 'label' => Yii::t('app', 'ТП (назначено)')],
            ['attribute' => 'moderate', 'label' => Yii::t('app', 'ТП (в работе)')],
            ['attribute' => 'completed', 'label' => Yii::t('app', 'Выполнено')],
        ],
    ]); ?>
</div>
<div class="panel panel-flat">
    <?= GridView::widget([
        'dataProvider' => $dataProviderList,
        'summary' => '',
        'tableOptions' => [
            'class' => $classList
        ],
        'showFooter' => true,
        'columns' => [
            'id',
            ['attribute' => 'surveyFilial.survey.name', 'label' => Yii::t('app', 'Название')],
            ['attribute' => 'surveyFilial.filial.cityLabel', 'label' => Yii::t('app', 'Город')],
            ['attribute' => 'surveyFilial.filial.filialText', 'label' => Yii::t('app', 'Объект')],
            ['attribute' => 'surveyFilial.surveyScenario.name', 'label' => Yii::t('app', 'Сценарий')],
            ['attribute' => 'ms.name', 'label' => Yii::t('app', 'Тайный покупатель')],
            ['attribute' => 'status', 'label' => Yii::t('app', 'Статус'), 'value' => function ($model) {
                return Yii::$app->utilityService->getSurveyStatusLabel($model->status);
            }],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'template' => '{view} {attach}',
                'buttons' => [
                    'attach' => function ($url, $model) {
                        if (!$model->ms_id) {
                            return Html::button('<i class="icon-man" style="color: #1E88E5;" data-tooltip="'
                                . Yii::t('app', 'Прикрепить покупателя')
                                . '" data-position="top" title="Добавить"></i>', ['survey-filial' => $model->survey_filial, 'ms-survey' => $model->id, 'class' => 'btn btn-default btn-xs custom_button', 'style' => 'border:none; background:none']);
                        }elseif ($model->status === MsSurvey::STATUS_MS_ASSIGNED || $model->status === MsSurvey::STATUS_IN_PROCESS){
                            return '&nbsp; ' . Html::a(
                                '<i class="glyphicon glyphicon-trash" style="color: red" data-tooltip="'
                                . Yii::t('app', 'Отвязать')
                                . '" data-position="top" title="Отвязать"></i>', ['unlink-ms', 'ms_survey_id' => $model->id, 'survey_filial_id' => $model->survey_filial], [
                                'data-pjax' => 1,
                                'data' => [
                                    'confirm' => Yii::t('app', 'Вы уверены что хотите удалить запись?'),
                                    'method' => 'post',
                                ],
                            ]
                            );
                        }
                    }
                ]
            ],
        ],
    ]); ?>
</div>
<script>
        $('.custom_button').click(function () {
            var id = $(this).attr('survey-filial');
            var msSurvey = $(this).attr('ms-survey');
            $.ajax({
                type: 'GET',
                url: "link-ms?survey_filial_id=" + id + "&ms_survey_id=" + msSurvey + "",
                success: function (data) {
                    $('#modalContent').html(data);
                }
            });
            $('#modal').modal();
        });
</script>
<div id="modal" class="fade modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div id='modalContent'></div>
            </div>
        </div>
    </div>
</div>