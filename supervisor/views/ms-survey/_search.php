<?php

use common\models\User;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use common\models\MsSurvey;

/* @var $this yii\web\View */
/* @var $model \supervisor\models\MsSurveySearchForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="panel panel-body">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row text-center">
        <div class="col-md-12">
            <div class="form-group">
                <div class="radio">
                    <label class="radio-inline">
                        <input type="radio" name="MsSurveySearchForm[status]" value="<?= MsSurvey::STATUS_MODERATION ?>"
                            <?= $model->status == MsSurvey::STATUS_MODERATION ? 'checked':"" ?>>
                        <?php $text = Yii::$app->utilityService->getSurveyStatusLabel(MsSurvey::STATUS_MODERATION);
                        echo  "<span class='label label-warning'>{$text}</span>"?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="MsSurveySearchForm[status]" value="<?= MsSurvey::STATUS_MODERATION_START ?>"
                            <?= $model->status == MsSurvey::STATUS_MODERATION_START ? 'checked':"" ?>>
                        <?php $text = Yii::$app->utilityService->getSurveyStatusLabel(MsSurvey::STATUS_MODERATION_START);
                        echo  "<span class='label label-info'>{$text}</span>"?>

                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="MsSurveySearchForm[status]" value="<?= MsSurvey::STATUS_COMPLETED ?>"
                            <?= $model->status == MsSurvey::STATUS_COMPLETED ? 'checked':"" ?>>
                        <?php $text = Yii::$app->utilityService->getSurveyStatusesForSupervisor()[MsSurvey::STATUS_COMPLETED];
                        echo  "<span class='label label-success'>{$text}</span>"?>
                    </label>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'geo_country')->dropDownList(Yii::$app->geoService->getCountriesAsMap(),
                ['id' => 'country'])->label(Yii::t('app', 'Выберите страну')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'city_id')->dropDownList(
                Yii::$app->geoService->getCitiesAsMap($model->geo_country),
                ['id' => 'city', 'prompt'=> 'Все'])->label( Yii::t('app', 'Выберите город')) ?>
        </div>
        <div class="col-md-12">
            <?php
            $fio = empty($model->ms_id) ? '' : \common\models\User::findOne($model->ms_id)->name;

            echo $form->field($model, 'ms_id')->widget(Select2::classname(), [
                'initValueText' => $fio, // set the initial display text
                'options' => ['placeholder' => Yii::t('app', 'Поиск тайного покупателя ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' =>'/user/search-dropdown?role='.User::ROLE_MYSTIC.'&client_id='.$client_id,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(model) { return model.text; }'),
                    'templateSelection' => new JsExpression('function (model) { return model.text; }'),
                ],
            ])->label(false);
            ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Применить'), [
            'class' => 'btn btn-primary'
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).ready(function () {
        $('#country').change(function () {
            $('#city').prepend($('<option></option>').attr('value', '').text('Все'));
        });
    })
</script>