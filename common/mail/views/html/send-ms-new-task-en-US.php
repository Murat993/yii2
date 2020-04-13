<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
/* @var $id */
/* @var $ms_survey_id */
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

    <p>У Вас появилось новое задание - номер <?= $ms_survey_id ?></p>

    <p>Для просмотра перейдите по ссылке:
        <a href="<?= Yii::$app->params['msDomain'] ?>survey/view?id=<?= $id ?>&ms_survey_id=<?= $ms_survey_id?>"><?= Yii::$app->params['msDomain'] ?></a>
    </p>


    <p>С уважением,<br>
        <a href="http://plan.com.kz">http://plan.com.kz</a><br>
        Служба поддержки: <a href="mailto:ms@plan.com.kz">ms@plan.com.kz</a></p>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>