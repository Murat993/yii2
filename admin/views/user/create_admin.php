<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\User */
?>
<div class="user-create">

    <?= $this->render('register_form', [
        'model' => $model,
    ]) ?>

</div>
