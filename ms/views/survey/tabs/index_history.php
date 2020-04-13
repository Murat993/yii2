<?php

use common\models\MsSurvey;
use yii\grid\GridView;
use yii\helpers\Html;
use common\models\Task;

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => '',
    'tableOptions' => [
        'class' => 'table datatable-basic'
    ],

    'columns' => [
        'id',
        [
            'attribute' => 'status',
            'format' => 'html',
            'value' => function ($data) {
                $text = Yii::$app->utilityService->getSurveyStatusLabel(MsSurvey::STATUS_COMPLETED);
                return "<span class='label label-success'>{$text}</span>";
            }
        ],
        'surveyFilial.survey.name',
        [
            'attribute' => 'surveyFilial.filial.filialText',
            'label' => Yii::t('app', 'Объект(адрес)')
        ],
        'surveyFilial.survey.survey_from',
        'surveyFilial.survey.survey_to',
        ['label' => Yii::t('app', 'Кол-во заданий'),
            'value' => function ($model) {
                return Task::find()->where(['survey_id' => $model->surveyFilial->survey_id])->count();
            }
        ]
    ],
]); ?>
