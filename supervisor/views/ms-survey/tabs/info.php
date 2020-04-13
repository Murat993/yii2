<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: narims
 * Date: 17.07.18
 * Time: 14:13
 */
$employee = $model->employee;
?>

<style>
    .employee-image-index img {
        max-width: 200px;
        max-height: 200px;
    }
</style>
<div class="row">
    <div class="col-md-4">

        <?= DetailView::widget([
            'model' => $model,
            'template' => function ($attribute, $index, $widget) {
                if ($attribute['value']) {
                    return "<li class='ms-info__item'>
                          <div class='ms-info__name'>{$attribute['label']}</div>
                          <div class='ms-info__value'>{$attribute['value']}</div
                        </li>";
                }
            },
            'options' => [
                'tag' => 'ul',
                'class' => 'ms-info__list'
            ],
            'attributes' => [
                'surveyFilial.survey.name',
                [
                    'attribute' => 'client_id',
                    'value' => function ($data) {
                        return $data->surveyFilial->survey->client->name;
                    },
                    'label' => Yii::t('app', 'Клиент')
                ],

                'surveyFilial.survey.comment'
            ],
        ]) ?>
    </div>
    <div class="col-md-4">
        <?= DetailView::widget([
            'model' => $model,
            'template' => function ($attribute, $index, $widget) {
                if ($attribute['value']) {
                    return "<li class='ms-info__item'>
                          <div class='ms-info__name'>{$attribute['label']}</div>
                          <div class='ms-info__value'>{$attribute['value']}</div
                        </li>";
                }
            },
            'options' => [
                'tag' => 'ul',
                'class' => 'ms-info__list'
            ],
            'attributes' => [
                [
                    'attribute' => 'surveyFilial.survey.survey_from',
                    'value' => function ($data) {
                        return "{$data->surveyFilial->survey->survey_from} - {$data->surveyFilial->survey->survey_to}";
                    }
                ],
                [
                    'attribute' => 'questionary_id',
                    'value' => function ($data) {
                        return $data->surveyFilial->survey->getQuestionaryLabel();
                    },
                    'label' => Yii::t('app', 'Шаблон')
                ],
                [
                    'attribute' => 'msSurveyDateVisited.employee_name',
                    'label' => Yii::t('app', 'Имя консультанта'),
                    'value' => function ($data) {
                        return $data->msSurveyDateVisited->employee_name ?: '__________';
                    }
                ],
                [
                    'attribute' => 'msSurveyDateVisited.date',
                    'label' => Yii::t('app', 'Дата и время визита'),
                    'value' => function ($data) {
                        return $data->msSurveyDateVisited->date ?: '__________';
                    }
                ],
                [
                    'attribute' => 'surveyFilial.survey.instruction',
                    'value' => function ($data) {
                        return Html::a(
                            Yii::t('app', 'Редактировать'),
                            ['ms-survey/update-date-visited', 'ms_survey_id' => $data->id, 'id' => $data->msSurveyDateVisited->id],
                            ['class' => 'btn btn-primary']
                        );
                    },
                    'label' => false
                ],
                [
                    'attribute' => 'surveyFilial.survey.instruction',
                    'value' => function ($data) {
                        if ($data->surveyFilial->instruction) {
                            $instruction = $data->surveyFilial->instruction;
                        } elseif ($data->surveyFilial->survey->instruction){
                            $instruction = $data->surveyFilial->survey->instruction;
                        }
                        return Html::a(Yii::t('app', 'Скачать инструкцию'), ['download', 'instruction' => $instruction], ['class' => 'btn btn-success']);
                    },
                    'label' => false
                ],
                [
                    'attribute' => 'surveyFilial.survey.instruction',
                    'value' => function ($data) {
                        if ($data->surveyFilial->instruction) {
                            $instruction = $data->surveyFilial->instruction;
                        } elseif ($data->surveyFilial->survey->instruction){
                            $instruction = $data->surveyFilial->survey->instruction;
                        }
                        return Html::a(Yii::t('app', 'Открыть инструкцию'), ['view-instruction', 'instruction' => $instruction], ['class' => 'btn btn-info', 'target' => '_blank']);
                    },
                    'label' => false
                ]
            ],
        ]) ?>
    </div>
    <?php if ($employee) : ?>

        <div class="col-md-4">

            <ul class="ms-info__list">
                <li class="ms-info__item">
                    <div class="ms-info__name"><?= $model->getAttributeLabel('Фото') ?></div>
                    <div class="ms-info__value ms-info__img">
                        <?php if ($employee->photo) : ?>
                            <?= Html::a(Html::img($employee->getImageUrl(), ['alt' => 'Нет фото']), $employee->getImageUrl(), ['class' => 'fancybox employee-image-index']); ?>
                        <?php else: ?>
                            <img src="https://d2x5ku95bkycr3.cloudfront.net/App_Themes/Common/images/profile/0_200.png">
                        <?php endif; ?>
                    </div>
                </li>
                <li class="ms-info__item">
                    <div class="ms-info__name"><?= $model->getAttributeLabel('Сотрудник') ?></div>
                    <div class="ms-info__value"><?= $employee->name . "($employee->positionLabel)"; ?></div>
                </li>
            </ul>


        </div>

    <?php endif; ?>
</div>
<script>
    $('.fancybox').fancybox();
</script>
