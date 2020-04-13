<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
?>
<div class="article-update">

    <h4 class="modal-title text-center"><?= Yii::t('app', 'Редактировать раздел')?></h4>
    <?= $this->render('_form', [
        'model' => $model,
        'questionary_id' => $questionary_id,
        'client_id' => $client_id
    ]) ?>

</div>
