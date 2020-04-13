<?php

use yii\helpers\Html;
use \common\models\MsSurveyDate;
use common\models\MsSurvey;

/**
 * Created by PhpStorm.
 * User: narims
 * Date: 17.07.18
 * Time: 14:13
 */
?>
<?php
if ($msStatus == MsSurvey::STATUS_MS_ASSIGNED) {
    echo '<form action="/survey/start-survey">';
    echo Html::hiddenInput('ms_survey_id', $ms_survey_id);
    echo Html::hiddenInput('id', $model->id);
    echo Html::button(Yii::t('app', 'Начать выполнение'), [
        'class' => 'btn btn-primary',
        'style' => 'margin-top:10px; margin-bottom:10px',
        'type' => 'submit'
    ]);
    echo '</form>';
}
?>
<style>
    .employee-image-index img {
        max-width: 200px;
        max-height: 200px;
    }
</style>
<div class="row">
    <div class="col-md-4">
        <ul class="ms-info__list">

            <li class="ms-info__item">
                <div class="ms-info__name"><?= $model->getAttributeLabel('name') ?></div>
                <div class="ms-info__value"><?= $model->name; ?></div>
            </li>
            <li class="ms-info__item">
                <div class="ms-info__name"><?= $model->getAttributeLabel('comment') ?></div>
                <div class="ms-info__value"><?= $msSurvey->comment ?: '_______'; ?></div>
            </li>
            <?php if ($instruction) : ?>
                <li class="btn-group">
                    <?= Html::a(Yii::t('app', 'Скачать инструкцию'),
                        ['survey/download', 'instruction' => $instruction],
                        ['class' => 'btn btn-success']) ?>
                    <?= Html::a(Yii::t('app', 'Открыть инструкцию'),
                        ['survey/view-instruction', 'instruction' => $instruction],
                        ['class' => 'btn btn-info', 'target' => '_blank']) ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="col-md-4">
        <ul class="ms-info__list">
            <li class="ms-info__item">
                <div class="ms-info__name"><?= $model->getAttributeLabel('survey_from') ?></div>
                <div class="ms-info__value"><?= MsSurveyDate::formatDate($msSurvey); ?></div>
            </li>
        </ul>
    </div>

    <?php if ($employee) : ?>

        <div class="col-md-4">

            <ul class="ms-info__list">
                <li class="ms-info__item">
                    <div class="ms-info__name"><?= $model->getAttributeLabel('Фото'); ?></div>
                    <div class="ms-info__value ms-info__img">
                        <?php if ($employee->photo) : ?>
                            <?= Html::a(Html::img($employee->getImageUrl(), ['alt' => 'Нет фото']), $employee->getImageUrl(), ['class' => 'fancybox employee-image-index']); ?>
                        <?php else: ?>
                            <img src="https://d2x5ku95bkycr3.cloudfront.net/App_Themes/Common/images/profile/0_200.png">
                        <?php endif; ?>
                    </div>
                </li>
                <li class="ms-info__item">
                    <div class="ms-info__name"><?= $model->getAttributeLabel('Сотрудник'); ?></div>
                    <div class="ms-info__value"><?= $employee->name . "($employee->positionLabel)"; ?></div>
                </li>
            </ul>


        </div>

    <?php endif; ?>


</div>

<?php if ($visit): ?>
    <div class="panel">

    </div>
<?php endif; ?>
<script>
    $('.fancybox').fancybox();
</script>