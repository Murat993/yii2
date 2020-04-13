<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
$user_id = $model->id;
?>

<div class="panel panel-flat">
    <div class="panel-body">

        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => true
        ]); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                <div class="form-group">
                    <?= Html::a('Сбросить пароль', ['reset-password', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
                    <?= Html::a('Задать пароль', [''], ['class' => 'btn btn-primary change-pass']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'role')->dropDownList(Yii::$app->utilityService->getUserRoleList()) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'comment')->textarea(['rows' => '7']) ?>
            </div>
        </div>
        <?= $form->field($model, 'status')->checkbox(['label' => 'Активирован']) ?>
    </div>

    <div class="panel-footer"><a class="heading-elements-toggle"><i class="icon-more"></i></a>
        <div class="heading-elements">
            <div class="pull-right">
                <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
if ($model->role === \common\models\User::ROLE_MYSTIC || $model->role === \common\models\User::ROLE_MYSTIC_GLOBAL) {
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => 'Нет записей.',
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'geo_unit_id',
                'value' => function ($data) {
                    return $data->getCityLabel();
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Действия'),
                'headerOptions' => ['width' => '80'],
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model) use ($user_id) {
                        return Html::a(
                            '<i class="glyphicon glyphicon-trash" data-tooltip="'
                            . Yii::t('app', 'Удалить')
                            . '" data-position="top" title="Удалить"></i>', ['unlink-geo', 'id' => $model->id, 'user_id' => $user_id], [
                                'data-pjax' => 1,
                                'data' => [
                                    'confirm' => Yii::t('app', 'Вы уверены что хотите удалить запись?'),
                                    'method' => 'post',
                                ],
                            ]
                        );
                    },
                ],
            ],
        ],
    ]);
    echo Html::button('Добавить', ['class' => 'btn btn-primary', 'id' => 'modalButton']);
}
?>
<div id="modal" class="fade modal" role="dialog">
    <div class="modal-dialog modal-sm">
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
<script>
    $(document).ready(function () {
        $('.change-pass').click(function (e) {
            e.preventDefault();
            $.ajax({
                type: 'GET',
                url: '<?= Url::to("change-password?user_id={$model->id}") ?>',
                success: function (data) {
                    $('#modalContent').html(data);
                    $('#modal').modal();
                }
            });
        });


        $('#modalButton').click(function () {
            $.ajax({
                type: 'GET',
                url: '<?= Url::to("add-city?user_id={$model->id}") ?>',
                success: function (data) {
                    $('#modalContent').html(data);
                    $('#modal').modal();
                }
            });
        });
    });
</script>


