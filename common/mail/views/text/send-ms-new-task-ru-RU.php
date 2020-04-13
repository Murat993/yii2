<?php

/** @var \yii\web\View $this view component instance */
/** @var \yii\mail\MessageInterface $message the message being composed */
/** @var string $content main view render result */
/* @var $id */
/* @var $ms_survey_id */
?>

<?php $this->beginPage() ?>
<?php $this->beginBody() ?>
У Вас появилось новое задание - номер <?= $id ?>. 
Для просмотра перейдите по ссылке: <?= Yii::$app->params['msDomain'] ?>survey/view?id=<?= $id ?>&ms_survey_id=<?= $ms_survey_id?>

С уважением,
http://plan.com.kz
Служба поддержки: ms@plan.com.kz

<?php $this->endBody() ?>
<?php $this->endPage() ?>
