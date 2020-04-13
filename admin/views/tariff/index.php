<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TariffSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$dataProvider->sort = false;
?>
<p>
    <?= Html::a(Yii::t('app', 'Создать'), ['create'], ['class' => 'btn btn-success']) ?>
</p>
<div class="panel panel-flat">
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => 'Нет записей.',
        'summary' => '',
        'tableOptions' => [
            'class' => 'table datatable-basic'
        ],
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['width' => '80'],
            ],
            'name',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'header' => 'Действия',
                'headerOptions' => ['width' => '80']
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
