<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'country_id')->dropDownList(Yii::$app->geoService->getCountriesAsMap(), ['id' => 'country' ,'class' => 'form-control']); ?>

    <?= $form->field($model, 'geo_unit_id')->dropDownList([], ['id' => 'city','class' => 'form-control']); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $(document).ready(function () {
        if ($('#country').val() !== ''){
            triggerCitySelect($(this).val());
        }

        $('#country').change(function () {
            if ($(this).val() !== ''){
                triggerCitySelect($(this).val());
            }
        });

        function triggerCitySelect(id) {
            var data = $.ajax({
                type: 'GET',
                url: "/geo/get-cities?id_country=" + id + "",
                dataType: 'json',
                context: document.body,
                global: false,
                async: false,
                success: function (data) {
                    return data;
                }
            }).responseText;
            var parsed = JSON.parse(data);
            $('#city').empty();
            $.each(parsed, function (key, value) {
                $('#city').append($('<option></option>').attr('value', key).text(value));
            });
        }
    });
</script>
