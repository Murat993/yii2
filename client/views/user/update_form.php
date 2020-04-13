<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">
    <?php Pjax::begin() ?>
    <?php $form = ActiveForm::begin([]); ?>
    <div class="text-right">
        <?= Html::a('Сбросить пароль',['reset-password','id'=>$model->id], ['class' => 'btn btn-primary'])?>
    </div>
    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>

    <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => true
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role')->dropDownList(Yii::$app->utilityService->getUserRoleList()) ?>

    <?= $form->field($model, 'status')->checkbox(['label' => 'Активирован']) ?>

    <?= $form->field($model, 'comment')->textarea() ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
