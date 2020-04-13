<?php

use common\models\User;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \supervisor\models\MsSurveySearchForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="survey-search">

    <?php $form = ActiveForm::begin([
        'action' => ['stats'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'geo_country')->dropDownList(Yii::$app->geoService->getCountriesAsMap(),
                ['id' => 'country'])->label(Yii::t('app', 'Выберите страну')) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'city_id')->dropDownList(
                Yii::$app->geoService->getCitiesAsMap($model->geo_country),
                ['id' => 'city', 'prompt'=> 'Все'])->label( Yii::t('app', 'Выберите город')) ?>
        </div>
        <div class="col-md-3"><?= $form->field($model, 'date')->textInput(['class' => 'form-control pickadate-translated-ru'])->label(Yii::t('app', 'Дата')) ?></div>
        <div class="col-md-3">
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
                        'url' =>'/user/search-dropdown?role='.User::ROLE_MYSTIC.','.User::ROLE_MYSTIC_GLOBAL.'&client_id='.$client_id,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(model) { return model.text; }'),
                    'templateSelection' => new JsExpression('function (model) { return model.text; }'),
                ],
            ])->label('Поиск тайного покупателя');
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
