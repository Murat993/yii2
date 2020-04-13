<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $form yii\widgets\ActiveForm */
$levelArticles = Yii::$app->articleService->getArticlesAsMap($model->questionary, $model->parent_id);
ArrayHelper::remove($levelArticles, $model->id);

?>
<h5>
    <div class="text-center">
        <p><?= "Раздел: {$header}" ?></p>
    </div>
</h5>
<div class="panel panel-body">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')
                ->dropDownList(Yii::$app->articleService->getClientArticlesAsMap($client_id), ['class' => 'select-search']); ?>
            <?= $form->field($model, 'parent_id')
                ->dropDownList($parentArticles, ['prompt' => '']); ?>
            <?= $form->field($model, 'move_after')
                ->dropDownList($levelArticles, ['prompt' => '', 'id' => 'move-after']); ?>
            <?= $form->field($model, 'move_before')
                ->dropDownList($levelArticles, ['prompt' => '', 'id' => 'move-before']); ?>
        </div>

        <div class="col-md-6">
            <div>Изменение порядка разделов</div>
            <div class="row">
                <div class="col-md-6 col-md-offset-6">
                    <div class="pull-right"><?= Html::submitButton(Yii::t('app', 'Разместить перед'), ['class' => 'btn btn-info form-group', 'id' => 'before-button']) ?></div>
                    <div class="pull-right"><?= Html::submitButton(Yii::t('app', 'Разместить после'), ['class' => 'btn btn-info form-group', 'id' => 'after-button']) ?></div>
                </div>
            </div>
            <div>Замена раздела</div>
            <div class="row mb-3">
                <div class="col-md-6 col-md-offset-6">
                    <div class="form-group pull-right"><?= Html::submitButton(Yii::t('app', 'Заменить'), ['class' => 'btn btn-warning change-template']) ?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-md-offset-6">
                    <div class="pull-right"><?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?></div>
                </div>
            </div>

        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
<script>
    $('.field-article-name').hide();
    $('#move-before').parent().hide();
    $('#move-after').parent().hide();
    $(document).ready(function () {
        $('.select-search').select2({
            tags: true,
            createTag: function (params) {
                var term = $.trim(params.term);

                if (term === '') {
                    return null;
                }
                return {
                    id: "tag:" + term,
                    text: term,
                    newTag: true // add additional parameters
                }
            }
        });

        $('.change-template').on('click', function (e) {
            e.preventDefault();
            $('.field-article-name').show();
        });
        $('#before-button').on('click', function (e) {
            e.preventDefault();
            $('#move-before').parent().show();
            $('#move-after').parent().hide();
            $('#before-button').hide();
            $('#after-button').show();
            $('#move-after').val(null);
        });
        $('#after-button').on('click', function (e) {
            e.preventDefault();
            $('#move-after').parent().show();
            $('#move-before').parent().hide();
            $('#after-button').hide();
            $('#before-button').show();
            $('#move-before').val(null);
        });
    });
</script>
