<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DriverProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$class = $dataProvider->getCount() ? 'table datatable-advanced datatable-selection-single' : 'table';
?>

<div class="row">
  <div class="col-md-2">
	  <?= Html::a(Yii::t('app', 'Создать'), ['create'], ['class' => 'btn btn-success']) ?>
  </div>
  <div class="col-md-3 pull-right text-right">
	<?php Pjax::begin() ?>
	<?php $form = ActiveForm::begin([]); ?>

	  <?= Html::dropDownList('statusFilter', '', $statuses, ['class' => 'btn btn-default dropdown-toggle', 'prompt' => 'Все']) ?>
	  <?= Html::submitButton(Yii::t('app', 'Поиск'), ['class' => 'btn btn-primary']) ?>
  </div>
</div>

<?php ActiveForm::end(); ?>
<?php Pjax::end() ?>
<div class="panel panel-flat">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{pager}\n{items}",
        'emptyText' => 'Список пуст',
        'tableOptions' => [
            'class' => $class
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
