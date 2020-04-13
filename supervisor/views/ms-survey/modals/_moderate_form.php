<?php

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\QuestionCheck;

/**
 * Just for ajax rendering
 */
?>

<?php $form = ActiveForm::begin(['options' => [
    'id' => 'moderate-form',
    'enctype' => 'multipart/form-data',
    'data-url' => Url::to(['moderate-answer', 'scenario' => $qn_check->scenario]),
    'method' => 'post'
]]);
$comment = $question->questionLang[0]->comment;
?>
<style>
    .field-questioncheck-answer_option_id {
        margin-top: 10px;
    }
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 id="modal-title" class="modal-title"><?= $title ?></h4>
</div>
<div class="modal-body modal-parent-body" style="padding-bottom: 0">
    <span>
        <span class="text-center">
            <h4>
            <?= "{$question->article->name}" ?>
        </h4>
        </span>
        <h5>
            <?= "{$question->name}" ?>
        </h5>
        <small><?= $comment ?></small>
        <textarea class="form-control question-answer"><?= $questionAnswer->text ?></textarea><br>
        <button type="button" class="btn btn-success submit-btn-answer save_answer" data-question_id="<?= $questionAnswer->id ?>">
            Сохранить изменения
        </button>
    </span>
    <?php $options = $question->answerOptions; ?>
    <?php if ($qn_check->scenario == QuestionCheck::SCENARIO_OPTIONS): ?>
        <?= $options ? $form->field($qn_check, 'answer_option_id')->radioList(
            ArrayHelper::map($options, 'id', 'text'), [
            'item' => function ($index, $label, $name, $checked, $value) use ($qn_check, $options) {
                $checked = $qn_check->answer_option_id == $value ? "checked" : "";
                $optionsColors = ArrayHelper::map($options, 'id', 'color');
                return '<div class="radio">'
                    . '<label style="background-color: ' . $optionsColors[$value] . '; color:white;"><input type="radio" class="answer-radio" ' . $checked . ' name="' . $name
                    . '" value="' . $value . ' " >' . $label . '</label>'
                    . '</div>';
            }
        ])->label(Yii::t('app', 'Выберите вариант ответа:')) : '<br>'
            . Yii::t("app", "Нет вариантов ответа") ?>
    <?php elseif ($qn_check->scenario === QuestionCheck::SCENARIO_OPTIONS_MULTI): ?>
        <?= $options ? $form->field($qn_check, 'answer_option_id')->checkboxList(
            ArrayHelper::map($options, 'id', 'text'), [
            'item' => function ($index, $label, $name, $checked, $value) use ($qn_check, $answer_id, $options) {
                $optionsColors = ArrayHelper::map($options, 'id', 'color');
                $checks = QuestionCheck::findAll(['answer_id' => $answer_id]);
                if (is_array($checks)) {
                    $map = ArrayHelper::map($checks, 'id', 'answer_option_id');
                }
                $checked = ArrayHelper::isIn($value, $map) ? 'checked' : "";
                return '<div class="checkbox">'
                    . '<label style="background-color: ' . $optionsColors[$value] . '; color:white; border:black;"><input type="checkbox" class="answer-checkbox" ' . $checked . ' name="' . $name
                    . '" value="' . $value . ' ">' . $label . '</label>'
                    . '<div>';
            }
        ])->label(Yii::t('app', 'Выберите вариант ответа:')) : '<br>'
            . Yii::t("app", "Нет вариантов ответа") ?>
    <?php else: ?>
        <?= $form->field($qn_check, 'text_answer')->textarea(['id' => 'answer-textarea', 'placeholder' => $questionAnswer->text]) ?>
    <?php endif; ?>
    <?= $form->field($qn_check, 'skip')->checkbox(['id' => 'skip-checkbox']) ?>
    <?= $form->field($qn_check, 'comment')->textarea(['id' => 'comment-textarea']) ?>
    <?= $form->field($qn_check, 'question_id', ['template' => '{input}'])->hiddenInput(
        ['value' => $question->id]) ?>
    <?= $form->field($qn_check, 'answer_id', ['template' => '{input}'])->hiddenInput(
        ['value' => $questionAnswer->id]) ?>
</div>
<?php if ($completed == 0): ?>
    <div class="modal-footer" style="padding-bottom: 0">
        <button type="submit" class="btn btn-success submit-btn-answer" data-question_id="<?= $questionAnswer->id ?>">
            Сохранить
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
<?php else: ?>
<?php endif; ?>
<?php ActiveForm::end() ?>
<script>
    $("#moderate-form").attr('action', $("#moderate-form").data("url") + '&ms_survey_id=' + $('#moderate-modal-content')
        .data('ms_survey_id'))
</script>
<?php
$script = <<< JS
    $(document).ready(function() {
      $('.submit-btn-answer').on('mousedown', function() {
          var questionAnswerInput = $(this).parents('.modal-parent-body').find('.question-answer');
          var questionId = $(this).data('question_id');
              $.ajax({
                url : '/ms-survey/update-question-answer',
                type : 'GET',
                data: {
                  questionAnswer: $('.question-answer').val(),         
                  questionId: questionId         
                },              
                success : function(data) {
                }                
            }); 
      })
      
    })
    
    $(document).on('click', '.save_answer', function () {
          var questionId = $(this).data('question_id');
              $.ajax({
                url : '/ms-survey/update-question-answer',
                type : 'GET',
                data: {
                  questionAnswer: $('.question-answer').val(),         
                  questionId: questionId         
                },              
                success : function(data) {
                    alert('Изменения сохранены');
                    location.reload();
                }                
            });
    });

JS;
$this->registerJs($script);
?>

