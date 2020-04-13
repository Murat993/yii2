<?php

use yii\widgets\ActiveForm;
use leandrogehlen\treegrid\TreeGrid;
use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: narims
 * Date: 17.07.18
 * Time: 14:01
 */
$title = Yii::t('app', 'Разделы');
$supervisor_id = Yii::$app->user->getId();
?>
<?php $form = ActiveForm::begin([
        'id' => 'answers-form',
        'action' => '/ms-survey/publish-survey?ms_survey_id='.$ms_survey_id
]) ?>

<style>

    .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: rgba(238, 238, 238, 0.4);
    }
</style>

<div class="filial-structure-unit-index">
    <h3>№ <?= $model->id ?>, | <?= $model->surveyFilial->filialCityLabel; ?>, <?= $model->surveyFilial->filial->name; ?>, <?= $model->surveyFilial->surveyScenario->name; ?>   </h3>
    <span style="float: right;margin: 10px"><?= Yii::t('app', 'Статус заполнения:') ?> <span id="ans-percent"></span>% (<span id="ans-completed"></span>/<span id="ans-total"></span>)</span>
    <?= TreeGrid::widget([
        'id' => 'ans-tree-grid',
        'dataProvider' => $dataProvider,
        'keyColumnName' => 'id',
        'parentColumnName' => 'parent_id',
        'pluginOptions' => [
            'initialState' => 'expanded',
        ],
        'columns' => [
            [
                'attribute' => 'name',
                'header' => "{$title}",
                'format' => 'raw',
                'value' => function ($data)use($answersForm, $form, $ms_id, $ms_survey_id, $completed, $supervisor_id) {
                    return $this->render('_question_view', ['data' => $data, 'form' => $form, 'ms_id' => $ms_id, 'ms_survey_id' => $ms_survey_id, 'completed' => $completed]);
                }

            ]
        ]
    ]);?>
</div>
<?php if($status == \common\models\MsSurvey::STATUS_MODERATION_START): ?>
<div class="form-group" style="margin-top:20px">
    <?= Html::submitButton('Опубликовать', [
        'class' => 'btn btn-success pull-right']) ?>
</div>
<?php endif; ?>
<?php ActiveForm::end() ?>
<?= $this->render('../modals/moderate_modal', ['ms_survey_id' => $ms_survey_id]) ?>

<?php
$script = <<< JS
$(document).ready(function() {
    function countQs() {
        var completed = $('.text-success.moderate-modal-a').length;
        var total = $('.moderate-modal-a').length
        $('#ans-completed').text(completed);  
        $('#ans-total').text(total);
        var percent = completed * 100 / total;
        if(percent != 0 && percent != 100){
            percent = percent.toFixed(1)
        }
        $('#ans-percent').text(percent);
    }
    
    function formScenarioSkip() {
        $("#comment-textarea").prop('disabled', false);
        $(".answer-radio").prop('disabled', true);
        $(".answer-checkbox").prop('disabled', true);
        $("#answer-textarea").prop('disabled', true);
    }
    
    function formScenarioCheck(){
         $("#comment-textarea").prop('disabled', true);
         $(".answer-radio").prop('disabled', false);
         $(".answer-checkbox").prop('disabled', false);
         $("#answer-textarea").prop('disabled', false);
    }
    
    function moderate(obj) {
          $('#moderate-modal').modal('show');
         //todo edit ajax
         $.ajax({
            url : '/ms-survey/render-moderate-form?question_id='+obj.data('q-id')+'&answer_id='+obj.data('a-id') + '&completed=' + obj.data('completed'),
            type : 'GET',                
            success : function(data) { 
                  $('#moderate-modal-content').html(data);
                  if($('#skip-checkbox').is(":checked")){
                      formScenarioSkip()
                  }else {
                      formScenarioCheck()
                  }
            }                
        });  
    }
   countQs();
  $('.collapse').collapse("show");  
  $(document).on('click', '.moderate-modal-a', function() {
            moderate($(this))
  });
  $(document).on('change', '#skip-checkbox', function() {
            if($(this).is(":checked")){
                formScenarioSkip()
            }else {
                formScenarioCheck()
            } 
  });  
  if("$open_modal" == 1){      
         var warn = $('.text-warning.moderate-modal-a.no-comment');         
         if(warn.length > 0){
             setTimeout(function() {
               moderate($('.text-warning.moderate-modal-a.no-comment').first())
             }, 300);
             
         } else {
             var warn_no_comment = $('.text-warning.moderate-modal-a.comment'); 
             if(warn_no_comment.length > 0){
                 setTimeout(function() {
                   moderate($('.text-warning.moderate-modal-a.comment').first())
                 }, 300);
                 
             }
         }
  }
  $('[data-toggle="popover"]').popover();
})
JS;
$this->registerJs($script);
?>
