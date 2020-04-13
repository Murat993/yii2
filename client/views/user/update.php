<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */

?>
<div class="user-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="panel panel-body">

        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => true
        ]); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <!--    --><?//= $form->field($model, 'role')->dropDownList(Yii::$app->utilityService->getUserRoleList()) ?>

        <?= $form->field($model, 'status')->checkbox(['label' => 'Активирован']) ?>

        <?= $form->field($model, 'comment')->textarea() ?>


        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-info']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
