<?php

use yii\helpers\Html;
use common\models\Question;

/**
 * Created by PhpStorm.
 * User: narims
 * Date: 18.07.18
 * Time: 10:27
 */
$questions = Question::find()
    ->where(['article_id' => $data->id])
    ->with([
        'currentTranslate', // loadabing data associated with the current translation
        'hasTranslate' // need to display the status was translated page
    ])->joinWith(['questionAnswer' => function ($query) use ($ms_survey_id) {
        $query->onCondition(['ms_survey_id' => $ms_survey_id])->from(['qa1' => 'question_answer']);
    }])
    ->all();
$userId = Yii::$app->user->getId();
?>
<?= "<h5 class='noselect'>{$data->name}</h5>" ?>
<?php if ($questions): ?>
    <a style="float: right" href="javascript:void(0)" data-toggle="collapse" data-target="#demo<?= $data->id ?>">
        <u><i><?= Yii::t('app', 'вопросы') ?></i></u></a>
    <br>
    <br>
    <div id="demo<?= $data->id ?>" class="collapse">

        <?php foreach ($questions as $qn): ?>

            <div class="row" style="display: flex ">
                <div class="col-md-4" style="width: 100%">
                    <?php $answerId = $qn->questionAnswer->id;
                    echo $form->field($model, 'texts[]')->textInput([
                        'maxlength' => true,
                        'class' => 'js-answer form-control' . ($qn->required ? ' answer-required' : ''),
                        'autocomplete'=>"off",
                        'value' => $qn->questionAnswer->text,
                        'data-question-ids' => $qn->id,
                        'data-ids' => $answerId,
                        'data-survey_id' => $survey_id,
                        'data-ms_survey_id' => $ms_survey_id,
                    ])->label("<p class='noselect'>" . $qn->name . ($qn->required ? "<span style='color: red'> * </span>" : "") . "</p>") ?>
                </div>
            </div>
            <?= Html::hiddenInput('AnswersForm[q_ids][]', $qn->id) ?>
            <?= Html::hiddenInput('AnswersForm[ids][]', $answerId) ?>
        <?php endforeach; ?>

    </div>
<?php endif; ?>
<?php
$script = <<< JS

$(document).ready(function() {
    $('#ans-tree-grid tbody').on('blur','input', function() {
        var text = $(this).val();
        var question_ids = $(this).data('question-ids');
        var ids = $(this).data('ids');
        var survey_id = $(this).data('survey_id');
        var ms_survey_id = $(this).data('ms_survey_id');
        
       $.ajax({
            url : '/survey/save-answer-on-blur',
            type : 'GET',
            data: {
              text: text,  
              question_ids: question_ids,  
              ids: ids,  
              survey_id: survey_id,  
              ms_survey_id: ms_survey_id  
            },             
            success : function(data) {
            },
        });
    })

})
JS;
$this->registerJs($script);
?>
<style>
    .form-group {
        width: 100%;
        display: flex;
        justify-content: flex-start;
    }

    .js-answer {
        width: 50%;
    }

    .control-label {
        width: 60%;
    }
</style>