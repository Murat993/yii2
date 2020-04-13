<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */
?>
<div class="user-view">

  <div class="row">
    <div class="col-md-6">

      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title no-margin text-bold"><strong><?= Html::encode( $this->title ) ?><a
                      class="heading-elements-toggle"></strong></a></h2>
          <div class="heading-elements">
            <div class="heading-btn">
				<?= Html::a( Yii::t( 'app', 'Update' ), [
					'/user/update',
					'id' => $model->id
				], [ 'class' => 'btn btn-primary' ] ) ?>
				<?= Html::a( Yii::t( 'app', 'Delete' ), [ '/user/delete', 'id' => $model->id ], [
					'class' => 'btn btn-danger',
					'data'  => [
						'confirm' => Yii::t( 'app', 'Are you sure you want to delete this item?' ),
						'method'  => 'post',
					],
				] ) ?>
            </div>
          </div>
        </div>

        <div class="panel-body">

          <ul class="user__list">
            <li>
              <span><?= $model->getAttributeLabel( 'name' ) ?></span>:
				<?= $model->name; ?>
            </li>
            <li>
              <span><?= $model->getAttributeLabel( 'phone' ) ?></span>:
				<?= $model->phone; ?>
            </li>
            <li>
              <span><?= $model->getAttributeLabel( 'email:' ) ?></span>
				<?= $model->email; ?>
            </li>
			  <?php if ( $model->comment ) : ?>
                <li>
                  <h6 class="content-group">
                    <i class="icon-comment-discussion position-left"></i>
                    <span>Коментарий: </span>
                  </h6>
                  <blockquote>
                    <p> <?= $model->comment; ?></p>
                  </blockquote>
                </li>
			  <?php endif; ?>
            <li>
              <span> <?= $model->getAttributeLabel( 'role' ) ?>:</span>
				<?= Yii::$app->utilityService->getRoleLabel( $model->role ) ?>
            </li>
            <li>
              <span> <?= $model->getAttributeLabel( 'status' ) ?></span>
				<?php
				switch ($model->status){
                    case 1:
                      $class = 'label-success';
                      break;
                    case 9:
                      $class = 'label-danger';
                      break;
                    case 2:
                      $class = 'label-default';
                        break;
                } ;?>
              <span class="label <?= $class ?>"><?= Yii::$app->utilityService->getStatusLabel( $model->status ) ?></span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

</div>
