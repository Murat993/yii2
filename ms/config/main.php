<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php', require __DIR__ . '/../../common/config/params-local.php', require __DIR__ . '/params.php', require __DIR__ . '/params-local.php'
);

return [
    'id' => 'plan-ms',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'sourceLanguage' => 'en',
    'controllerNamespace' => 'ms\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-ms',
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
                        'app'       => 'app.php',
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
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
