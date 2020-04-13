<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

?>
<div class="user-update">

    <?= $this->render('update_form', [
        'model' => $model,
        'dataProvider' => $dataProvider
    ]) ?>

</div>
