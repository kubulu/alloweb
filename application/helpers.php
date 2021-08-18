<?php

/**
 * @return string
 */
function version() {
    return '1.6';
}

/**
 * @return bool
 */
function isInstalled() {
    return file_exists(dirname(__FILE__) . '/../' . '.env');
}

/**
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = false) {

    $value = isset($_ENV[$key]) ? $_ENV[$key] : getenv($key);

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;
    }

    return $value;
}

function dd($data) {
    array_map(function ($x) {
        echo (new \yii\helpers\VarDumper())->dumpAsString($x, 10, true);
    }, func_get_args());
    die(1);
}
