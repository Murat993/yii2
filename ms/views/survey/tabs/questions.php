<?php

use kartik\datetime\DateTimePicker;
use yii\widgets\ActiveForm;
use leandrogehlen\treegrid\TreeGrid;
use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: narims
 * Date: 17.07.18
 * Time: 14:01
 */
$settingsLength = Yii::$app->systemSettingsService->getSystemSettings()->answerLength;
$answerLength = (int)$settingsLength ?: 0;
?>
<?php $form = ActiveForm::begin([
    'id' => 'answers-form',
    'action' => \yii\helpers\Url::to(['save-answers', 'survey_id' => $survey_id, 'ms_survey_id' => $ms_survey_id])
]) ?>
<div class="row">
    <div class="col-md-12">
        <?php
        echo $form->field($answersForm, 'visit_date')->widget(DateTimePicker::classname(),
            [
            'pluginOptions' => [
                    'class' => 'has-feedback has-feedback-left',
                    'value' => '18-06-1018, 14:45',
                    'autoclose'=>true,
            ]
        ])->label(Yii::t('app', "Дата и время визита"))
        ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($answersForm, 'employee', [
            'template' => "{label}\n{hint}\n{input}\n\n{error}",
            'options' => [
//        'class' => 'has-feedback has-feedback-left'
            ]])
            ->textInput(['class' => 'form-control input-lg']) ?>
    </div>
</div>


<div class="filial-structure-unit-index">
  <span style="float: right;margin: 10px"><?= Yii::t('app', "Статус заполнения:") ?> <span
              id="ans-percent"></span>% (<span
              id="ans-completed"></span>/<span id="ans-total"></span>)</span>
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
                'label' => Yii::t('app', 'Разделы'),
                'format' => 'raw',
                'value' => function ($data) use ($answersForm, $form, $ms_survey_id, $survey_id) {
                    return $this->render('_question_view', ['data' => $data,
                        'model' => $answersForm,
                        'form' => $form,
                        'ms_survey_id' => $ms_survey_id,
                        'survey_id' => $survey_id,
                    ]);
                }

            ]
        ]
    ]); ?>
</div>
<?php
if ($msStatus == \common\models\MsSurvey::STATUS_IN_PROCESS): ?>
    <div class="form-group" style="margin-top:20px">
        <?= Html::submitButton(Yii::t('app', 'Отправить на модерацию'), [
            'class' => 'btn btn-success',
            'id' => 'submitb',
            'type' => 'button',
            'data-ms_survey_id' => $ms_survey_id,
            'data-ms_id' => Yii::$app->user->id
        ]) ?>
        <br><?= Yii::t('app', "*Кнопка отправить на модерацию будет активна только когда
    тайный покупатель заполнит ответы на все вопросы и выполнит все задания.") ?>
    </div>
<?php endif; ?>

<?php ActiveForm::end() ?>
<script>
    $(document).ready(function () {
        $('#anytime-both').AnyTime_picker({
            labelDayOfMonth: 'День',
            labelYear: 'Год',
            labelHour: 'Часы',
            labelMinute: 'Минуты',
            labelMonth: 'Месяц',
            labelTitle: 'Дата визита',
            askSecond: false,
            askYear: false,
            dayAbbreviations: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            monthAbbreviations: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            hiddenInput: true

        });
        $('.collapse').collapse("show");
        var inputs = $('.js-answer');

        function updateAnswersState() {
            var total = inputs.length;
            $('#ans-total').text(total);
            var completed = 0;
            var percent = 0;
            var minAnswerChars = <?= $answerLength ?>;
            inputs.each(function (index) {
                var elem = $(this);
                if (elem.val()) {
                    if (elem.val().length >= minAnswerChars) {
                        completed++;
                        elem.parent().removeClass("has-error");
                        elem.parent().addClass("has-success");
                    }
                } else {
                    if (elem.hasClass("answer-required")) {
                        if (minAnswerChars === 0) {
                            elem.parent().addClass("has-success");
                        } else {
                            elem.parent().addClass("has-error");
                        }
                    }
                    elem.parent().removeClass("has-success");
                }
            });
            $('#ans-completed').text(completed);
            percent = completed * 100 / total;
            if (percent != 0 && percent != 100) {
                percent = percent.toFixed(1)
            }
            $('#ans-percent').text(percent);
        }

        updateAnswersState();
        inputs.on('input', function (e) {
            if ($(this).hasClass("valid") && $(this).val().length >= <?= $answerLength ?>) {
                return;
            }
            updateAnswersState()
        });
        $(document).on('click', '#submitb', function (e) {
            e.preventDefault();
            if ($(".has-error").length > 0) {
                var answerLength =  <?= $answerLength ?>;
                $('#ans-tree-grid').treegrid('expandAll');
                if (answerLength >= 0) {
                    var text = "Необходимо заполнить все поля. Минимальное количество символов для ответа - ";
                    alert(text.concat(answerLength));
                } else {
                    alert("Необходимо заполнить все поля.");
                }


                return false;
            }
            $.ajax({
                url: '/survey/are-tasks-completed?ms_id=' + $(this).data('ms_id') + '&ms_survey_id=' + $(this).data('ms_survey_id'),
                type: 'GET',
                success: function (data) {
                    if (data == 1) {
                        $('#answers-form').submit();
                    } else {
                        alert("Необходимо заполнить все задания")
                    }
                }
            });

        });

        $('#answersform-employee').blur(function (e) {

            $.ajax({
                url : '/survey/update-employee-on-blur?ms_survey_id=' + <?= $ms_survey_id ?>,
                type : 'get',
                data: {
                    employee: $(this).val()
                },
                success : function(data) {
                },
            });
        });

        $('#answersform-visit_date').change(function (e) {

            $.ajax({
                url : '/survey/update-datetime-on-blur?ms_survey_id=' + <?= $ms_survey_id ?>,
                type : 'get',
                data: {
                    datetime: $(this).val()
                },
                success : function(data) {
                },
            });
        });

    })
</script>