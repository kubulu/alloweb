<?php

/**
 * Load application environment from .env file
 */
$dotEnvPath = dirname(__FILE__) . '/../';
$dotEnvFile = defined('YOUDATE_TEST') ? '.env-test' : '.env';
if (file_exists($dotEnvPath . $dotEnvFile)) {
    $dotEnv = new \Dotenv\Dotenv($dotEnvPath, $dotEnvFile);
    $dotEnv->load();
}

/**
 * Init application constants
 */
defined('YII_DEBUG') or define('YII_DEBUG', env('APP_DEBUG', true));
defined('YII_ENV') or define('YII_ENV', env('APP_ENV', 'dev'));
