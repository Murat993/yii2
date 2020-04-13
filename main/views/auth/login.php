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
            <div class="icon-object border-slate-300 text-slate-300"><i class="icon-reading"></i></div>
            <h5 class="content-group"><?= Yii::t('app', 'Login to your account') ?> <small class="display-block"><?= Yii::t('app','enter credentials')?></small></h5>
        </div>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'rememberMe')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'login'), ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
        </div>

        <div class ="text-center" style="color:#999;margin:1em 0">
            <?= Html::a(Yii::t('app', 'Forget password?'), ['auth/password-reset']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>