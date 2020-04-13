<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $form yii\widgets\ActiveForm */
$articles = Yii::$app->articleService->getArticlesAsMap($questionary_id);
?>

<div class="article-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->dropDownList(Yii::$app->articleService->getClientArticlesAsMap($client_id), ['class' => 'select-search', 'prompt' => '']); ?>

    <?= $form->field($model, 'parent_id')->dropDownList($articles, ['prompt'=> '']) ?>

    <?= $form->field($model, 'move_after')
        ->dropDownList($articles, ['prompt' => '', 'id' => 'move-after']); ?>
    <?= $form->field($model, 'move_before')
        ->dropDownList($articles, ['prompt' => '', 'id' => 'move-before']); ?>

    <div class="form-group">
        <div class="pull-right"><?= Html::submitButton(Yii::t('app', 'Разместить перед'), ['class' => 'btn btn-info form-group', 'id' => 'before-button']) ?></div>
        <div class="pull-right"><?= Html::submitButton(Yii::t('app', 'Разместить после'), ['class' => 'btn btn-info form-group', 'id' => 'after-button']) ?></div>
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $('#move-before').parent().hide();
    $('#move-after').parent().hide();
    $(document).ready(function() {
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
