<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="panel panel-flat">
    <div class="panel-body">
        <?php yii\widgets\Pjax::begin() ?>
        <?php $form = ActiveForm::begin([
            'id' => 'user-form',
            'validationUrl' => ['/client/validate-user'],
            'enableAjaxValidation' => true
        ]); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'name', ['template' => '{label}{input}{error}'])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+7(999)999-9999',
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'email', ['template' => '{label}{input}{error}'])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'role')->dropDownList(Yii::$app->utilityService->getRoleListForClient(), ['id' => 'role']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'country_id')->dropDownList(Yii::$app->geoService->getCountriesAsMap(), ['id' => 'country']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'city_id')->dropDownList([], ['id' => 'cities']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'comment')->textarea(['rows' => '7']) ?>
            </div>
        </div>
        <?= $form->field($model, 'status')->checkbox(['label' => 'Активирован']) ?>
        <div class="panel-footer"><a class="heading-elements-toggle"><i class="icon-more"></i></a>
            <div class="heading-elements">
                <div class="pull-right">
                    <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end() ?>
</div>
<script>
    function hideBlocks() {
        $('#cities').hide();
        $('#cities').parent().hide();
        $('#country').hide();
        $('#country').parent().hide();
    }

    function showBlocks() {
        $('#country').show();
        $('#country').parent().show();
        $('#cities').show();
        $('#cities').parent().show();
    }

    hideBlocks();

    $(document).ready(function () {
        $('#role').change(function () {
            switch ($(this).val()) {
                case '222':
                    showBlocks();
                    if ($('#country').val() !== '') {
                        triggerCitySelect($('#country').val());
                    }
                    break;
                case '777':
                    showBlocks();
                    if ($('#country').val() !== '') {
                        triggerCitySelect($('#country').val());
                    }
                    break;
                default:
                    hideBlocks();
                    break;
            }
        });
        $('#country').change(function () {
            if ($(this).val() !== '') {
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
            $('#cities').empty();
            $.each(parsed, function (key, value) {
                $('#cities').append($('<option></option>').attr('value', key).text(value));
            });
        }
    });
</script>

