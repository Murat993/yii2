<?php

use yii\helpers\Html;
use \common\models\MsSurvey;

$this->title = $model->surveyFilial->survey->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Анкеты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php
                if ($model->status == MsSurvey::STATUS_MODERATION) {
                    echo '<form method="get" action="/ms-survey/start-survey">';
                    echo Html::hiddenInput('id', $model->id);
                    echo Html::button(Yii::t('app', 'Начать проверку'), [
                        'class' => 'btn btn-primary',
                        'style' => 'margin:10px',
                        'type' => 'submit'
                    ]);
                    echo '</form>';
                }
                ?>
                <ul class="nav nav-tabs">
                    <?php if ($model->status === MsSurvey::STATUS_MODERATION):?>
                        <li class="disabled">
                            <a data-toggle="tab disabled" href="#tab-questions" data-key="1">
                                <?= Yii::t('app', 'Вопросы') ?>
                            </a>
                        </li>
                        <li class="disabled">
                            <a data-toggle="tab disabled" href="#tab-tasks" data-key="2">
                                <?= Yii::t('app', 'Задания') ?>
                            </a>
                        </li>

                        <li class="disabled">
                            <a data-toggle="tab disabled" href="#tab-info" data-key="3">
                                <?= Yii::t('app', 'Общая информация') ?>
                            </a>
                        </li>
                    <?php else:
                        ?>
                        <li class="active">
                            <a data-toggle="tab" href="#tab-questions" data-key="1">
                                <?= Yii::t('app', 'Вопросы') ?>
                            </a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tab-tasks" data-key="2">
                                <?= Yii::t('app', 'Задания') ?>
                            </a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tab-info" data-key="3">
                                <?= Yii::t('app', 'Общая информация') ?>
                            </a>
                        </li>
                    <?php endif;?>
                </ul>

                <div class="tab-content">
                    <div id="tab-info" class="tab-pane fade">
                        <?= $this->render('/ms-survey/tabs/info', ['model' => $model]) ?>
                    </div>
                    <div id="tab-tasks" class="tab-pane fade">
                        <?= $this->render('/ms-survey/tabs/tasks', [
                            'dataProvider' => $taskDataProvider,
                            'ms_id' => $model->ms_id,
                            'ms_survey_id' => $model->id,
                            'id' => $id]) ?>
                    </div>
                    <div id="tab-questions" class="tab-pane fade <?= $model->status == MsSurvey::STATUS_MODERATION ? "" : "in active" ?>">
                        <?= $this->render('/ms-survey/tabs/questions', [
                            'model' => $model,
                            'dataProvider' => $questionDataProvider,
                            'answersForm' => $answersForm,
                            'survey_id' => $model->surveyFilial->survey_id,
                            'ms_id' => $model->ms_id,
                            'ms_survey_id' => $model->id,
                            'status' => $model->status,
                            'open_modal' => $open_modal,
                            'completed' => $completed]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS

function activateTab(tab){
    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
}

$(document).ready(function() {
    var tabNum = parseInt("$tab");
    if(tabNum === 1){
        activateTab('tab-questions')
    }else if(tabNum === 2){
        activateTab('tab-tasks')
    }else if(tabNum === 3){
        activateTab('tab-info')
    } 
})
JS;
$this->registerJs($script);
?>



