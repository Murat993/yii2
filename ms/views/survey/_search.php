<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\MsSurvey;

/* @var $this yii\web\View */
/* @var $model common\models\search\FilialSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="survey-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="form-group">
        <div class="radio">
            <label class="radio-inline">
                <input type="radio" name="status" value="<?= MsSurvey::STATUS_MS_ASSIGNED ?>"
                    <?=
                    $status == MsSurvey::STATUS_MS_ASSIGNED ? 'checked':"" ?>>
                <?= Yii::$app->utilityService->getSurveyStatusLabel(MsSurvey::STATUS_NEW) ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="status" value="<?= MsSurvey::STATUS_IN_PROCESS ?>"
                    <?= $status == MsSurvey::STATUS_IN_PROCESS ? 'checked':"" ?>>
                <?= Yii::$app->utilityService->getSurveyStatusLabel(MsSurvey::STATUS_IN_PROCESS) ?>
            </label>
            <?= Html::submitButton(Yii::t('app', 'Применить'), [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-left:20px'
            ])?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
