<?php

/* @var $content string */

use main\assets\MainAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;


MainAsset::register($this);
?>
<?php $this->beginPage() ?>
<html lang="<?php echo Yii::$app->language ?>">
<head>
    <?php $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to(['../favicon.png'])]); ?>
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

<body class="login-container login-cover">
<?php $this->beginBody() ?>
<!-- Main navbar -->
<div class="navbar navbar-inverse">
    <div class="navbar-header">
        <a class="navbar-brand" href="../auth/index"><img src="/themes/plan/assets/images/logo.png" alt=""></a>
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
