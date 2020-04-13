<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 19.07.18
 * Time: 11:57
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<div class="panel panel-body login-form">
    <div class="text-center">
        <div class="icon-object border-warning text-warning"><i class="icon-spinner11"></i></div>
        <h5 class="content-group"><?= Yii::t('app', 'password-reset-message') ?>
            <small class="display-block"><?= Yii::t('app', 'reset credentials') ?></small>
        </h5>
    </div>

    <?php $form = ActiveForm::begin([
            'id' => 'password-reset',
        'enableAjaxValidation' => true,
        'validationUrl' => ['auth/validate-form']
    ]); ?>
    <div class="form-group has-feedback">
        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
        <div class="form-control-feedback">
            <i class="icon-mail5 text-muted"></i>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'reset'), ['class' => 'btn bg-blue btn-block', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
