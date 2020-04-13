<?php

/* @var $content string */

use main\assets\MainAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;


\supervisor\assets\SVAsset::register($this);
?>
<?php $this->beginPage() ?>
<html lang="<?php echo Yii::$app->language ?>">
<head>
<!--    --><?php //$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to(['../favicon.png'])]); ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet"
          type="text/css">
    <!-- /global stylesheets -->
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body class="login-container login">
<?php $this->beginBody() ?>
<!-- Main navbar -->
<div class="navbar navbar-inverse">
    <div class="navbar-header">
<!--        <a class="navbar-brand" href="../site/index"></a>-->
        <img src="/themes/plan/assets/images/logo.png" alt="">
        <ul class="nav navbar-nav pull-right visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
        </ul>
    </div>
    <div class="navbar-collapse collapse" id="navbar-mobile">

<!--        <div class="nav navbar-nav">-->
<!--            <div class="header__name">PLAN MSP</div>-->
<!--        </div>-->





        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown dropdown-user">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-user"></i>
                    <span>
                        <?= Yii::$app->user->identity->email ?></span>
                    <i class="caret"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    <?php
                    if (Yii::$app->user->isGuest) {
                        echo '<li class="divider"></li>
                            <li><a href="../site/login" ><i class="icon-switch2"></i> Войти</a></li>';
                    } else {
                        echo '<li class="divider"></li>
                            <li><a href="../site/logout" data-method="post"><i class="icon-switch2"></i> Выйти</a></li>';
                    }
                    ?>
                </ul>
            </li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-earth position-left"></i>
                    <?= Yii::t('app', 'language') ?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <?php $langs = \common\translate\models\Lang::getLocales();
                    foreach ($langs as $lang) {
                        echo "<li><a href='/site/change-language?ln={$lang->locale}' class='change-locale'>{$lang->id}</a></li>";
                    } ?>
                </ul>
            </li>
        </ul>

    </div>
</div>
<!-- /main navbar -->


<!-- Page container -->
<div class="page-container">

    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Content area -->
            <div class="content">
                <?php echo $content; ?>

                <!-- Footer -->
                <div class="footer text-muted">
                    <div class="footer__copyright text-center text-lg-left">
                        <span>&copy; 2018 PLAN®. All rights reserved. Powered by Luxystech.</span>
                    </div>
                </div>
                <!-- /footer -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</div>
<!-- /page container -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
