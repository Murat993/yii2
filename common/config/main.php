<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'name' => 'Plan',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'defaultDuration' => 12000
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    /* fileMap определяет, какой файл будет подключаться для определённой категории.
                    иначе так название категории является именем файла*/
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/filial-structure-unit/' => 'filial-structure-unit/index',
                '/scenario/' => 'scenario/index',
                '/user/' => 'user/index',
                '/filial/' => 'filial/index',
                '/employee/' => 'employee/index',
            ],
        ],
        'imagesService' => [
            'class' => 'common\services\ImagesService',
        ],
        'fileService' => [
            'class' => 'common\services\FileService',
        ],
        'imageHandler' => [
            'class' => '\maxlapko\components\handler\ImageHandler',
            'driver' => '\maxlapko\components\handler\drivers\GD', // DriverGD
            'driverOptions' => []
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'itemFile' => '@common/uploads/rbac/items.php',
            'assignmentFile' => '@common/uploads/rbac/assignments.php',
            'ruleFile' => '@common/uploads/rbac/rules.php',
        ],
        'authService' => [
            'class' => 'common\services\AuthService'
        ],
        'notification' => [
            'class' => 'common\services\notifications\NotificationService'
        ],
        'userService' => [
            'class' => 'common\services\UserService'
        ],
        'clientService' => [
            'class' => 'common\services\ClientService'
        ],
        'structService' => [
            'class' => 'common\services\StructureService'
        ],
        'utilityService' => [
            'class' => 'common\services\UtilityService'
        ],
        'employeeService' => [
            'class' => 'common\services\EmployeeService'
        ],
        'articleService' => [
            'class' => 'common\services\ArticleService'
        ],
        'questService' => [
            'class' => 'common\services\QuestService'
        ],
        'filialService' => [
            'class' => 'common\services\FilialService'
        ],
        'geoService' => [
            'class' => 'common\services\GeoService'
        ],
        'groupService' => [
            'class' => 'common\services\GroupService'
        ],
        'localeService' => [
            'class' => 'common\services\LocaleService'
        ],
        'surveyService' => [
            'class' => 'common\services\SurveyService'
        ],
        'systemSettingsService' => [
            'class' => 'common\services\SystemSettingsService'
        ],
        'langService' => [
            'class' => 'common\services\LangService'
        ],
        'reportService' => [
            'class' => 'common\services\ReportService'
        ],
        'treeBuilder' => [
            'class' => 'common\services\TreeBuilder'
        ],
    ],
];
