<?php

use common\models\MsSurvey;
use yii\grid\GridView;
use yii\helpers\Html;
use common\models\Task;
use \common\models\MsSurveyDate;

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
                switch ($data->status) {
                    case MsSurvey::STATUS_MS_ASSIGNED:
                        $colorClass = 'label label-primary';
                        break;
                    case MsSurvey::STATUS_IN_PROCESS:
                        $colorClass = 'label label-success';
                        break;
                    case MsSurvey::STATUS_COMPLETED:
                        $colorClass = 'label label-success';
                        break;
                    default:
                        $colorClass = 'label label-primary';
                        break;
                }
                $text = Yii::$app->utilityService->getSurveyStatusesForMS()[$data->status];
                return "<span class='{$colorClass}'>{$text}</span>";
            }
        ],
        'surveyFilial.survey.name',
        [
            'attribute' => 'surveyFilial.filial.filialText',
            'label' => Yii::t('app', 'Объект(адрес)')
        ],
        [
            'label' => Yii::t('app', 'Период'),
            'value' => function ($model) {
                return MsSurveyDate::formatDate($model);
            }
        ],
        [
            'label' => Yii::t('app', 'Кол-во заданий'),
            'value' => function ($model) {
                return Task::find()->where(['survey_id' => $model->surveyFilial->survey_id])->count();
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => Yii::t('app', 'Действия'),
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model) {
                    if ($model->status === MsSurvey::STATUS_MS_ASSIGNED) {
                        return Html::a('<span class=\'label label-success\'>Выполнить</span>', [
                            '/survey/view',
                            'id' => $model->surveyFilial->survey_id,
                            'ms_survey_id' => $model->id
                        ]);
                    } else {
                        return Html::a('<span class=\'label label-primary\'>Продолжить</span>', [
                            '/survey/view',
                            'id' => $model->surveyFilial->survey_id,
                            'ms_survey_id' => $model->id
                        ]);
                    }

                }
            ]
        ],
    ],
]); ?>
