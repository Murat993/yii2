<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use client\assets\ClAsset;


/* @var $this yii\web\View */
/* @var $searchModel common\models\DriverProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>


<div class="tbl-block">
    <div class="table-form">
        <?php Pjax::begin(); ?>
        <?php if (Yii::$app->user->can('client-superuser-permissions') && Yii::$app->userService->checkClientEdit()): ?>
            <div class="row" style="margin-bottom: 1rem !important;">
                <div class="col-md-4 text-left">
                    <?= Html::button(Html::tag('span', '', ['class' => 'icn icn-plus']) .
                        Yii::t('app', 'Создать пользователя'),
                        ['value' => \yii\helpers\Url::to('user/create'), 'class' => 'btn btn-info', 'id' => 'modalButton']); ?>
                </div>
                <div class="col-lg-8 text-right">
                    <?= Html::dropDownList('statusFilter', '', $statuses, ['class' => 'btn btn-default dropdown-toggle', 'prompt' => 'Все']) ?>
                    <?= Html::submitButton(Yii::t('app', 'Поиск'), ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        <?php else: ?>
        <div class="index__data mb-4">
            <div class="text-left">
                <h6>Выбор статуса</h6>
                    <?= Html::dropDownList('statusFilter', '', $statuses, ['class' => 'btn btn-default dropdown-toggle', 'prompt' => 'Все']) ?>
                    <?= Html::submitButton(Yii::t('app', 'Поиск'), ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="filias__table-wrap">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => '',
            'emptyText' => 'Список пуст',
            'tableOptions' => [
                'class' => 'table datatable-basic'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'name',
                [
                    'attribute' => 'role',
                    'value' => function ($data) {
                        return Yii::$app->utilityService->getRoleLabel($data->role);
                    }
                ],
                'registration_date',
                'phone',
                'email:email',
                [
                    'attribute' => 'status',
                    'value' => function ($data) {
                        return Yii::$app->utilityService->getStatusLabel($data->status);
                    }
                ],
                //'status',
                //'comment',

                //'last_login',
                //'role_id',

                [
                    'class' => \client\column\MyActiveColumn::className(),
                    'template' => '{permission} {update} {delete}',
                    'buttons' => [
                        'permission' => function ($a, $model) {
                            return Html::a(
                                '<i class="glyphicon glyphicon-lock" data-tooltip="'
                                . Yii::t('app', 'Права')
                                . '" data-position="top" title="Права"></i>', ['permissions', 'id' => $model->id], [
                                    'data-pjax' => 0
                                ]
                            );
                        }
                    ],
                    'visibleButtons' => [
                        'update' => (Yii::$app->user->can('client-superuser-permissions') && Yii::$app->userService->checkClientEdit()),
                        'delete' => (Yii::$app->user->can('client-superuser-permissions') && Yii::$app->userService->checkClientEdit()),
                        'permission' => (Yii::$app->user->can('client-superuser-permissions') && Yii::$app->userService->checkClientEdit())
                    ]
                ],
            ],
        ]); ?>
    </div>
    <?php Pjax::end(); ?>
</div>
<?php
$script = <<< JS
$(document).ready(function () {
   $('#modalButton').click(function () {
    $('#modal-filial').modal()
    .find('#modalContent')
    .load($(this).attr('value'));
    })
 $('.modalUpdateButton').click(function () {
        $('#modal-filial').modal('show')
        .find('#modalContent')
        .load($(this).data('url'));
    })
})
JS;
$this->registerJs($script);
?>
<div id="modal-filial" class="fade modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

            </div>
            <div class="modal-body">
                <div id='modalContent'></div>
            </div>

        </div>
    </div>
</div>

