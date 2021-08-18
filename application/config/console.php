<?php

use yii\helpers\ArrayHelper;

Yii::setAlias('@webroot', dirname(__DIR__) . '/../');

$db = require(__DIR__ . '/db.php');
$core = require(__DIR__ . '/core.php');

$config = [
    'id' => 'youdate-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        app\bootstrap\ConsoleBootstrap::class,
        'themeManager',
    ],
    'controllerNamespace' => 'app\commands',
    'controllerMap' => [
        'migrate' => [
            'class' => hauntd\core\migrations\MigrateController::class,
            'migrationPaths' => [
                '@app/migrations',
            ],
        ],
    ],
    'modules' => [],
    'components' => [
        'db' => $db,
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'view' => [
            'class' => app\base\View::class,
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}


$config = ArrayHelper::merge($core, $config);

return $config;
