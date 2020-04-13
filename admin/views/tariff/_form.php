<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Tariff */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="panel-body">

    <?php $form = ActiveForm::begin(); ?>
  <div class="form-group">
    <?= $form->field($model, 'name')->textInput() ?>
  </div>

        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>


    <?php ActiveForm::end(); ?>

</div>
