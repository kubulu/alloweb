<?php

return [
    'id' => 'youdate-installer',
    'name' => 'YouDate Installer',
    'language' => 'en-US',
    'basePath' => dirname(__FILE__) . '/../',
    'controllerNamespace' => 'installer\\controllers',
    'defaultRoute' => 'install/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'bootstrap' => [],
    'modules' => [],
    'runtimePath' => sys_get_temp_dir(),
    'components' => [
        'db' => [
            'class' => yii\db\Connection::class,
            'dsn' => null,
            'username' => null,
            'password' => null,
            'charset' => null,
        ],
        'cache' => [
            'class' => yii\caching\DummyCache::class,
        ],
        'settings' => [
            'class' => app\settings\Settings::class,
        ],
        'request' => [
            'cookieValidationKey' => 'YouDateInstaller',
        ],
        'errorHandler' => [
            'errorAction' => 'install/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'install/index',
                'install' => 'install/index',
            ],
        ],
    ],
];
