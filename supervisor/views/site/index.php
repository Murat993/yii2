<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
?>
<div class="site-index">
    <p>
        <?= Yii::t('app', 'welcome')?>
        <?= Yii::$app->language?>
        <?= Yii::$app->sourceLanguage?>
        <?= Yii::$app->session->get('lang'); ?>
    </p>
</div>
