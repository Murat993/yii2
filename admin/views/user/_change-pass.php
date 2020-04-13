<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="">

    <?php $form = ActiveForm::begin([
            'id' => 'pass-change-form',
        'enableAjaxValidation' => true,
        'validationUrl' => 'validate-pass-change'
    ]); ?>

    <?= $form->field($model, 'password')->passwordInput(); ?>
    <?= $form->field($model, 'password_repeat')->passwordInput(); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

