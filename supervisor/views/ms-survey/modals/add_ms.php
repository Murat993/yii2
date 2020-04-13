<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Survey */
/* @var $form yii\widgets\ActiveForm */
//'form-control daterange-basic'
?>

<div class="">
    <?php yii\widgets\Pjax::begin() ?>
    <?php $form = ActiveForm::begin([
            'id' => 'ms-form',
            'action' => ['/ms-survey/link-ms?survey_filial_id=' . $survey_filial_id . '&ms_survey_id=' . $ms_survey_id],
            'enableAjaxValidation' => true
    ]); ?>


    <?= $form->field($model, 'ms_id')->dropDownList($msList) ?>

    <?= $form->field($model, 'employee_id')->dropDownList($employeeList, ['prompt' => '']) ?>
<div class="text-center">
    <h5>Дата посещения</h5>
</div>
    <div class="text-center">
        <?php
        foreach (Yii::$app->utilityService->getMsDateTypeList() as $key => $type) {

            if ($key === 2) {
                echo Html::button("{$type}", ['class' => 'btn btn-primary daterange-ranges ml-10 mr-10', 'id' => $key]);
            } else {
                echo Html::button("{$type}", ['class' => 'btn btn-primary daterange-ranges', 'id' => $key]);
            }
        }
        ?>
    </div>

    <?= $form->field($model, 'date')->textInput(['class' => 'form-control date', 'visible' => false])->label(false) ?>

    <?= $form->field($model, 'date_type')->hiddenInput(['value' => ''])->label(false) ?>

    <?= $form->field($model, 'comment')->textarea(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>


    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end() ?>
</div>
<script>
    $(document).ready(function () {
        $('.date').hide();
        var hidden = true;

        $('.daterange-ranges').click(function () {
            if (hidden == true) {
                hidden = false;
                $('.date').show();
            }
            $('#linkmsform-date_type').val($(this).attr('id'));
            reinitPicker($(this).attr('id'));
        });

        function reinitPicker(type) {
            destroyPicker();
            initPicker(type);
        }

        function destroyPicker() {
            if ($('.date').data('daterangepicker')) {
                $('.date').data('daterangepicker').remove();
            }
            if ($('.date').data('pickadate')) {
                $('.date').data('pickadate').stop();
            }
        }

        function initPicker(type) {
            switch (type) {
                case '1':
                    $('.date').pickadate({
                        monthsFull: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                        weekdaysShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                        formatSubmit: 'yyyy/mm/dd',
                        today: 'сегодня',
                        clear: 'удалить',
                        format: 'yyyy-mm-dd',
                    });
                    break;
                case '2':
                    $('.date').daterangepicker({
                        locale: {
                            format: 'Y-MM-DD',
                            formatSubmit: 'yyyy/mm/dd',
                            applyLabel: 'Вперед',
                            cancelLabel: 'Отмена',
                            startLabel: 'Начальная дата',
                            endLabel: 'Конечная дата',
                            customRangeLabel: 'Выбрать дату',
                            daysOfWeek: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                            monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                            firstDay: 1
                        }
                    });
                    break;
                case '3':
                    alert('Пока не поддерживается, пожалуйста, выберите другой вариант');
                    break;
            }
        }
    });
</script>
<style>
    .daterangepicker {
        z-index: 1060 !important;
    }
</style>
