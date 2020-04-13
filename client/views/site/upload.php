<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Car */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
    .form-control {
        height: auto;
    }
</style>
<div class="index__data mb-4">

    <?php
    $form = ActiveForm::begin();
    ?>
    <div class="row">
        <div class="col-md-4 text-left">
            <?= $form->field($model, 'file')->fileInput(['class' => 'form-control', 'id' => 'file-input'])->label(Yii::t('app', 'Файл')); ?>
        </div>
    </div>

    <div class="text-left">
        <?= Html::submitButton(Yii::t('app', 'Загрузить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>