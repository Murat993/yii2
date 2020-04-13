<ul class="navigation navigation-main navigation-accordion">

    <li class="navigation-header"><span><?=Yii::t('app', 'Анкеты') ?></span>
        <i class="icon-menu" title="Отчеты"></i></li>

    <li><a href="/ms-survey/index"><i class="icon-home5"></i> <span>
                <?= Yii::t('app', 'Очередь на проверку') ?></span></a></li>

    <li class="navigation-header"><span><?= Yii::t('app', 'Функционал') ?></span>
        <i class="icon-menu" title="<?= Yii::t('app', 'Справочники') ?>"></i></li>
    <li><a href="/ms-survey/stats"><i class="icon-home5"></i> <span><?= Yii::t('app', 'График') ?>
            </span></a></li>

    <li class="navigation-header"><span><?= Yii::t('app', 'Другое') ?></span>
        <i class="icon-menu" title="Другое"></i></li>
    <li><a href="/user/reset-password"><i class="icon-home5"></i>
            <span><?= Yii::t('app', 'Смена пароля') ?></span></span></a></li>
</ul>
