<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \client\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Смена пароля');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
  <div class="col-lg-5">
<div class="panel panel-default">
  <div class="panel-body">
<div class="site-reset-password">
    <h1 class="no-margin-top"><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app', 'Введите ваш новый пароль, затем войдите заново:') ?></p>


            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                <?= $form->field($model, 'password')->textInput(['type' => 'password']) ?>
                <?= $form->field($model, 'confirm')->textInput(['type' => 'password']) ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
  </div>
</div>