<aside class="sidebar left-side">
    <nav class="sidebar__part">
        <?=
        \yii\widgets\Menu::widget([
            'options' => [
                'tag' => 'ul class="sidebar__nav"'
            ],
            'activateItems' => true,
            'activeCssClass' => 'sidebar-active',
            'items' => [
                [
                    'label' => Yii::t('app', 'Главная'),
                    'url' => ['/site/index'],
                    'template' => '<a href="{url}" class="sidebar__link_index">{label}</a>'
                ],
            ],
            'linkTemplate' => '<a href="{url}" class="sidebar__link_forms">{label}</a>',
        ])
        ?>
    </nav>

    <nav class="sidebar__part sidebar__part_second">
        <p class="sidebar__head"><?= Yii::t('app', 'Отчеты') ?></p>
        <?=
        \yii\widgets\Menu::widget([
            'options' => [
                'tag' => 'ul class="sidebar__nav"'
            ],
            'activateItems' => true,
            'activeCssClass' => 'sidebar-active',
            'items' => [
                [
                    'label' => Yii::t('app', 'Аналитика'),
                    'url' => ['/report/analytics'],
                    'template' => '<a href="{url}" class="sidebar__link_analytics">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Конструктор отчетов'),
                    'url' => ['/report/pivot'],
                    'template' => '<a href="{url}" class="sidebar__link_analytics">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Анкеты'),
                    'url' => ['/report/surveys'],
                    'template' => '<a href="{url}" class="sidebar__link_forms">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Сводка'),
                    'url' => ['/report/summary'],
                    'template' => '<a href="{url}" class="sidebar__link_report">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Объекты'),
                    'url' => ['/report/objects'],
                    'template' => '<a href="{url}" class="sidebar__link_objects">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Сотрудники'),
                    'url' => ['/report/employees'],
                    'template' => '<a href="{url}" class="sidebar__link_members">{label}</a>'
                ],
            ],
            'linkTemplate' => '<a href="{url}" class="sidebar__link_forms">{label}</a>',
        ])
        ?>
    </nav>

    <div class="sidebar__border"></div>

    <nav class="sidebar__part">
        <p class="sidebar__head"><?= Yii::t('app', 'Справочники') ?></p>
        <?=
        \yii\widgets\Menu::widget([
            'options' => [
                'tag' => 'ul class="sidebar__nav"'
            ],
            'activateItems' => true,
            'activeCssClass' => 'sidebar-active',
            'items' => [
                [
                    'label' => Yii::t('app', 'Компания'),
                    'url' => ['/client/index'],
                    'template' => '<a href="{url}" class="sidebar__link_companies">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Структура'),
                    'url' => ['/filial-structure-unit/index'],
                    'template' => '<a href="{url}" class="sidebar__link_structure">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Объекты'),
                    'url' => ['/filial/index'],
                    'template' => '<a href="{url}" class="sidebar__link_filials">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Пользователи'),
                    'url' => ['/user/index'],
                    'template' => '<a href="{url}" class="sidebar__link_users">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Сотрудники'),
                    'url' => ['/employee/index'],
                    'template' => '<a href="{url}" class="sidebar__link_members">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Сценарии'),
                    'url' => ['/scenario/index'],
                    'template' => '<a href="{url}" class="sidebar__link_scenario">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Выбор цвета'),
                    'url' => ['/report/change-color'],
                    'template' => '<a href="{url}" class="sidebar__link_members">{label}</a>'
                ],
            ],
            'linkTemplate' => '<a href="{url}" class="sidebar__link_forms">{label}</a>',
        ])
        ?>
    </nav>

    <div class="sidebar__border"></div>
    <?php if (Yii::$app->user->can('client-superuser-permissions') && Yii::$app->userService->checkClientEdit()): ?>
    <nav class="sidebar__part">
        <p class="sidebar__head"><?= Yii::t('app', 'Загрузка CSV') ?></p>
        <?=
        \yii\widgets\Menu::widget([
            'options' => [
                'tag' => 'ul class="sidebar__nav"'
            ],
            'activateItems' => true,
            'activeCssClass' => 'sidebar-active',
            'items' => [
                [
                    'label' => Yii::t('app', 'Структура объектов'),
                    'url' => ['/site/upload?type=structure'],
                    'template' => '<a href="{url}" class="sidebar__link_structure">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Объекты'),
                    'url' => ['/site/upload?type=object'],
                    'template' => '<a href="{url}" class="sidebar__link_filials">{label}</a>'
                ],
                [
                    'label' => Yii::t('app', 'Сотрудники'),
                    'url' => ['/site/upload?type=employee'],
                    'template' => '<a href="{url}" class="sidebar__link_members">{label}</a>'
                ],
            ],
            'linkTemplate' => '<a href="{url}" class="sidebar__link_forms">{label}</a>',
        ])
        ?>
    </nav>

    <?php endif; ?>

</aside>