<?php
/* @var $this \yii\web\View */

/* @var $content string */

use client\assets\ClAsset;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;

$originalId = Yii::$app->session->get('user.idbeforeswitch');
ClAsset::register($this);
?>
<?php $this->beginPage() ?>
<html lang="<?php echo Yii::$app->language ?>">
<head>
    <?php $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to(['../favicon.png'])]); ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> <?php echo Yii::$app->name . ', ' . Html::encode($this->title); ?></title>

    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body>
<div class="preloader" id="preloader">
    <?= Html::img(['/images/91.svg']) ?>
    <h5 class="preloader-text"> Идет обработка информации. Пожалуйста, подождите</h5>
</div>

<?php $this->beginBody() ?>

<header class="header">
    <div class="header__logo left-side">
        <img src="/themes/plan/assets/images/logo.png" alt="Логотип Plan Web">
    </div>

    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4 pl-0 header__block">
                    <span class="header__ver d-none d-lg-block">MSP v1.0</span>

                    <div class="header__logo d-block d-lg-none">
                        <span class="d-inline-block d-lg-none burger"></span>
                        <img src="/themes/plan/assets/images/logo.png" alt="Логотип Plan Web">
                    </div>
                </div>

                <div class="col-sm-8 pr-0 text-left text-sm-right header__block">
                    <div class="header__lang">
                        <?php $langs = \common\translate\models\Lang::getLocales();
                        foreach ($langs as $lang) {
                            $class = Yii::$app->language === $lang->locale ? 'active' : 'inactive';
                            echo "<a href='/site/change-language?ln={$lang->locale}' class='{$class} change-locale'>{$lang->id}</a>";
                        } ?>
                    </div>

                    <div class="header__profile profile float-right float-sm-none">
                        <div class="media">
                            <div class="media-body">
                                <span class="profile__name"><?= Yii::$app->user->identity->email ?></span>
                            </div>
                        </div>

                        <div class="profile__dropdown">
                            <a href="/user/reset-password"><i class="icon-home5"></i>
                                <span><?= Yii::t('app', 'Смена пароля') ?></span></a>
                            <a href="../site/logout" data-method="post"><i class="icon-switch2"></i> Выйти из
                                профиля</a>
                            <?php
                            if ($originalId){
                                echo '<a href="../site/switch-user" data-method="post"><i class="glyphicon glyphicon-user"></i> Вернуться в админку</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>


<?php
if (Yii::$app->user->identity->role == User::ROLE_CLIENT_SUPER || Yii::$app->user->identity->role == User::ROLE_CLIENT_USER) {
    echo $this->render("../include/menu");
}
?>


<div class="content-wrap right-side">
    <main class="content">
        <h1 class="content__title"><?= Html::encode($this->title) ?></h1>

        <div class="breadcrumbs">

            <?php
            echo Breadcrumbs::widget([
                'itemTemplate' => "<li><a href='{url}' class=\"breadcrumbs__link\"><span>{label}</span></a></li>\n",
                'homeLink' => [
                    'label' => Yii::t('app', 'mainpage'),
                    'url' => Yii::$app->homeUrl,
                    'template' => "<li><a href=\"/\" class=\"breadcrumbs__link index\"> </a></li>\n"
                ],
                'options' => ['class' => ''],
                'links' => $this->params['breadcrumbs']
            ]);
            ?>


        </div>
        <div id="preloader">

        </div>
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

    </main>


    <footer class="footer text-center text-md-left">
				<span class="footer__copyright">
					&copy; 2018. MSP v1.0. Все права принадлежат ТОО "Розница Караганда"
				</span>
    </footer>
</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<script>
    $(document).ready(function () {
        var preloader = $('#preloader');
        preloader.fadeOut('fast');
        $('.sidebar__link_analytics').click(function () {
            preloader.fadeIn();
        });
    });
</script>
