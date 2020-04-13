<?php
/* @var $this \yii\web\View */

/* @var $content string */

use ms\assets\MsAsset;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;


MsAsset::register($this);
?>
<?php $this->beginPage() ?>
    <html lang="<?php echo Yii::$app->language ?>">
    <head>
        <?php $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to(['../favicon.png'])]); ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title> <?php echo Yii::$app->name . ', ' . Html::encode($this->title); ?></title>
        <!-- Global stylesheets -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet"
              type="text/css">
        <!-- /global stylesheets -->
        <?= Html::csrfMetaTags() ?>
        <?php $this->head() ?>
    </head>

    <body class="navbar-top">
    <?php $this->beginBody() ?>
    <!-- Main navbar -->

    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-header">
            <a class="navbar-brand" href="../site/index">
                <!--              <img src="-->
                <?php //echo $this->assetBundles['admin\assets\ADAsset']->baseUrl ?><!--" alt="">-->
                <img src="/themes/plan/assets/images/logo.png" alt="">
            </a>
            <ul class="nav navbar-nav pull-left visible-xs-block">
                <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
                <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
            </ul>

        </div>


        <div class="navbar-collapse collapse" id="navbar-mobile">
            <div class="nav navbar-nav">
                <div class="header__name">PLAN MSP</div>
            </div>


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

            <!-- Main sidebar -->

            <!-- /main sidebar -->
            <!-- Main content -->
            <div class="content-wrapper">

                <!-- Page header -->

                <div class="page-header page-header-default">
                    <div class="page-header-content">
                        <div class="page-title">
                            <h5>
                              <span
                                      class="text-semibold"><?= Html::encode($this->title) ?></span>
                            </h5>
                        </div>

                        <div class="heading-elements">

                        </div>
                    </div>

                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <?php
                            echo Breadcrumbs::widget([
                                'itemTemplate' => "<li>{link}</li>\n",
                                'homeLink' => [
                                    'label' => Yii::t('app', 'mainpage'),
                                    'url' => Yii::$app->homeUrl
                                ],
                                'links' => $this->params['breadcrumbs']
                            ]);
                            ?>
                        </ul>


                    </div>
                </div>
                <!-- /page header -->


                <!-- Content area -->
                <div class="content">
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-check"></i>Успех!</h4>
                            <?= Yii::$app->session->getFlash('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-error alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-check"></i>Ошибка!</h4>
                            <?= Yii::$app->session->getFlash('error') ?>
                        </div>
                    <?php endif; ?>
                    <?php echo $content; ?>
                    <?= $this->render('agreement') ?>
                    <!-- Footer -->
                    <div class="footer text-muted">
                        <div class="footer__copyright text-center text-lg-left">
                            <span>&copy; 2018 PLAN®. All rights reserved.</span>
                        </div>
                    </div>
                </div>
                <!-- /footer -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->
    <!-- /page container -->
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
<?php if (Yii::$app->session->get('not-accepted')): ?>
    <script>
        $("#modal").modal('show');
    </script>
<?php endif; ?>