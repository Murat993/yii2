<?php

use supervisor\controllers\MsSurveyController;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MsSurveyDate */
/* @var $ms_survey_id */
/* @var $title */

$this->title = Yii::t('app', 'Обновить общую информацию - ' . $title, [
    'nameAttribute' => '' . $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Анкеты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['view', 'id' => $id, 'tab' => MsSurveyController::TAB_INFO]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ms-survey-date-update">

    <?= $this->render('_form-info', [
        'model' => $model,
        'ms_survey_id' => $ms_survey_id
    ]) ?>

</div>
