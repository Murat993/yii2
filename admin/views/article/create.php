<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = Yii::t('app', 'Добавить раздел');
?>
<div class="article-create">

    <?= $this->render('create_form', [
        'model' => $model,
        'questionary_id' => $questionary_id,
        'client_id' => $client_id,
    ]) ?>

</div>
