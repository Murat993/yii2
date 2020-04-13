<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php', require __DIR__ . '/../../common/config/params-local.php', require __DIR__ . '/params.php', require __DIR__ . '/params-local.php'
);

return [
    'id' => 'plan-main',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'main\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-login',
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
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
            'loginUrl'=> ['auth/login'],
            'identityCookie' => [
                'name' => '_identity-global',
                'httpOnly' => true,
                'domain' => $params['cookieDomain']
            ],
        ],
        'session' => [
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
            'errorAction' => 'auth/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.yandex.ru',  // e.g. smtp.mandrillapp.com or smtp.gmail.com
                'username' => 'noreply@plan.com.kz',
                'password' => 'EUTzPKqEdj0H',
                'port' => '465', // Port 25 is a very common port too
                'encryption' => 'ssl', // It is often used, check your provider or mail server specs
            ],

        ],
    ],
    'params' => $params,
    'defaultRoute' => 'auth/index'
];
