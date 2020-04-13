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
        <h5 class="content-group"><?= Yii::t('app', 'password-reset-message') ?></h5>
        <h5 class="content-group"><?= $message ?></h5>
    </div>
    <div class="form-group has-feedback">
        <div class="form-control-feedback">

        </div>
    </div>
    <div class="form-group text-center">
        <?= Html::a(Yii::t('app', 'Назад'), ['auth/index'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
