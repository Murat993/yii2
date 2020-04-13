<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php', require __DIR__ . '/../../common/config/params-local.php', require __DIR__ . '/params.php', require __DIR__ . '/params-local.php'
);

return [
    'id' => 'plan-admin',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'admin\controllers',
    'sourceLanguage' => 'en',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-admin',
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    // 'sourceLanguage' => 'ru',
                    /* fileMap определяет, какой файл будет подключаться для определённой категории.
					иначе так название категории является именем файла*/
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'enableSession' => true,
            'authTimeout' => 86400,
            'identityCookie' => [
                'name' => '_identity-global',
                'httpOnly' => true,
                'domain' => $params['cookieDomain']
            ],
        ],
        'session' => [
//            'class' => 'yii\web\DbSession',
            'name' => 'plan-global-session',
            'cookieParams' => [
                'httpOnly' => true,
                'domain' => $params['cookieDomain']
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning','info'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module',
            'bsVersion' => '3',
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            'i18n' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@kvgrid/messages',
                'forceTranslation' => true
            ]
        ],
        'dynagrid' => [
            'class' => '\kartik\dynagrid\Module'
        ]
    ],
    'params' => $params,
];
