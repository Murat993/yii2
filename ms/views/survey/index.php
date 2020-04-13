<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\MsSurvey;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SurveySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="panel panel-flat">
    <ul class="nav nav-tabs">
        <li class="active">
            <a data-toggle="tab" href="#tab-info" data-key="1"">
            <?= Yii::t('app', 'Текущие') ?>
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#tab-tasks" data-key="2">
                <?= Yii::t('app', 'Выполнено') ?>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="tab-info" class="tab-pane fade in active">
            <?= $this->render('tabs/index_main', ['dataProvider' => $mainDataProvider]) ?>
        </div>
        <div id="tab-tasks" class="tab-pane fade">
            <?= $this->render('tabs/index_history', ['dataProvider' => $historyDataProvider]) ?>
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
    }
})
JS;
$this->registerJs($script);
?>