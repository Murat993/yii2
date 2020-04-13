<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MsSurveyDate */
/* @var $form yii\widgets\ActiveForm */
/* @var $ms_survey_id */
?>

<div class="ms-survey-date-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
    <?php
    echo $form->field($model, 'date')->widget(DateTimePicker::classname(),
        [
            'pluginOptions' => [
                'class' => 'has-feedback has-feedback-left',
                'value' => '18-06-1018, 14:45',
                'autoclose'=>true,
            ]
        ])->label(Yii::t('app', "Дата и время визита"))
    ?>
    </div>

<!--    --><?//= $form->field($model, 'date')->textInput(['style'=>'width:290px', 'class' => 'form-control input-lg', 'id'=>'anytime-both']) ?>

    <?= $form->field($model, 'employee_name')->textInput(['style'=>'width:290px', 'maxlength' => true]) ?>

    <?= $form->field($model, 'ms_survey_id')->hiddenInput(['value' => $ms_survey_id])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

