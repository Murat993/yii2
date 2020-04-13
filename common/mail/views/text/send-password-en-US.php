<?php

use yii\helpers\Html;

/** @var \yii\web\View $this view component instance */
/** @var \yii\mail\MessageInterface $message the message being composed */
/** @var string $content main view render result */
?>

<?php $this->beginPage() ?>
<?php $this->beginBody() ?>
Поздравляем! Вы были успешно зарегистрированы в системе!

Для входа в систему перейдите по ссылке https://plan.com.kz .

Для авторизации используйте Ваш емайл и пароль: <?= $password ?>

С уважением,
http://plan.com.kz
Служба поддержки: ms@plan.com.kz

<?php $this->endBody() ?>
<?php $this->endPage() ?>
