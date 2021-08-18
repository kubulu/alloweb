<?php

use yii\helpers\ArrayHelper;

$core = require(__DIR__ . '/core.php');

$config = [
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'default/index',
    'container' => [
        'definitions' => [
            yii\i18n\Formatter::class => [
                'currencyCode' => 'USD',
            ],
        ],
    ],
    'bootstrap' => [
        [
            'class' => app\bootstrap\WebBootstrap::class,
        ],
        'log',
        'themeManager',
    ],
    'modules' => [
        env('ADMIN_PREFIX') => [
            'class' => app\modules\admin\Module::class,
        ],
    ],
    'components' => [
        'authClientCollection' => [
            'class'   => yii\authclient\Collection::class,
            'clients' => [],
        ],
        'request' => [
            'cookieValidationKey' => env('APP_COOKIE_VALIDATION_KEY'),
        ],
        'user' => [
            'identityClass' => app\models\User::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['/security/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'db' => require(__DIR__ . '/db.php'),
        'view' => [
            'class' => app\base\View::class,
        ],
    ],
];

if (env('SOCIAL_FACEBOOK_APP_ID')) {
    $config['components']['authClientCollection']['clients']['facebook'] = [
        'class' => app\clients\Facebook::class,
        'clientId'  => env('SOCIAL_FACEBOOK_APP_ID'),
        'clientSecret' => env('SOCIAL_FACEBOOK_APP_SECRET'),
    ];
}

if (env('SOCIAL_TWITTER_CONSUMER_KEY')) {
    $config['components']['authClientCollection']['clients']['twitter'] = [
        'class' => app\clients\Twitter::class,
        'consumerKey' => env('SOCIAL_TWITTER_CONSUMER_KEY'),
        'consumerSecret' => env('SOCIAL_TWITTER_CONSUMER_SECRET'),
    ];
}

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap']['debug'] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap']['gii'] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

$config = ArrayHelper::merge($core, $config);

return $config;
