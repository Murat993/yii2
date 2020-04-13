<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<p>Поздравляем! Вы были успешно зарегистрированы в системе!</p>

<p>Для входа в систему перейдите по ссылке <a href="https://plan.com.kz">https://plan.com.kz.</a></p>

<p>Для авторизации используйте Ваш емайл и пароль: <?= $password ?></p>

<p>С уважением,<br>
<a href="http://plan.com.kz">http://plan.com.kz</a><br>
Служба поддержки: <a href="mailto:ms@plan.com.kz">ms@plan.com.kz</a></p>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
