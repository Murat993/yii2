<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Анкеты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="col-md-3 pull-right text-right ">
        <?= Html::a(Yii::t('app', 'Назад'), ['/survey/index'], ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <?php if ($msStatus !== \common\models\MsSurvey::STATUS_IN_PROCESS): ?>
                            <li class="active">
                                <a data-toggle="tab" href="#tab-info" data-key="1">
                                    <?= Yii::t('app', 'Общая информация') ?>
                                </a>
                            </li>
                            <li class="disabled">
                                <a data-toggle="tab disabled" href="#tab-tasks" data-key="2">
                                    <?= Yii::t('app', 'Задания') ?>
                                </a>
                            </li>
                            <li class="disabled">
                                <a data-toggle="tab disabled" href="#tab-questions" data-key="3">
                                    <?= Yii::t('app', 'Вопросы') ?>
                                </a>
                            </li>
                        <?php else:
                            ?>
                            <li class="active">
                                <a data-toggle="tab" href="#tab-info" data-key="1">
                                    <?= Yii::t('app', 'Общая информация') ?>
                                </a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#tab-tasks" data-key="2">
                                    <?= Yii::t('app', 'Задания') ?>
                                </a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#tab-questions" data-key="3">
                                    <?= Yii::t('app', 'Вопросы') ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <div class="tab-content">
                        <div id="tab-info" class="tab-pane fade in active">
                            <?= $this->render('/survey/tabs/info', ['model' => $model, 'employee' => $employee,
                                'msStatus' => $msStatus, 'ms_survey_id' => $ms_survey_id, 'msSurvey' => $msSurvey, 'instruction' => $instruction]) ?>
                        </div>
                        <div id="tab-tasks" class="tab-pane fade">
                            <?= $this->render('/survey/tabs/tasks', ['dataProvider' => $taskDataProvider, 'ms_survey_id' => $ms_survey_id, 'id' => $id]) ?>
                        </div>
                        <div id="tab-questions" class="tab-pane fade">
                            <?= $this->render('/survey/tabs/questions', [
                                'dataProvider' => $questionDataProvider,
                                'answersForm' => $answersForm,
                                'survey_id' => $model->id,
                                'ms_survey_id' => $ms_survey_id,
                                'msStatus' => $msStatus
                            ]) ?>
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
        activateTab('tab-info')
    }else if(tabNum === 2){
        activateTab('tab-tasks')
    }else if(tabNum === 3){
        activateTab('tab-questions')
    }    
})
JS;
$this->registerJs($script);
?>