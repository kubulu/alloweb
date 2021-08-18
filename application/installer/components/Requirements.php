<?php

namespace installer\components;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package installer\components
 */
class Requirements
{
    public static function checkRequirements()
    {
        return [
            'phpCheck' => [
                'title' => 'PHP 7.0 or higher',
                'valid' => version_compare(PHP_VERSION , '7.0', ">=")
            ],
            'pdoCheck' => [
                'title' => 'PDO MySQL Driver is enabled',
                'valid' => extension_loaded('pdo') && extension_loaded('pdo_mysql'),
            ],
            'gdOrImagickCheck' => [
                'title' => 'GD or Imagick driver',
                'valid' => extension_loaded('gd') || extension_loaded('imagick'),
            ],
            'fileInfo' => [
                'title' => 'Fileinfo extension is enabled',
                'valid' => extension_loaded('fileinfo'),
            ],
            'exifCheck' => [
                'title' => 'EXIF extension is enabled',
                'valid' => extension_loaded('exif'),
            ],
            'mbStringCheck' => [
                'title' => 'Mbstring extension enabled',
                'valid' => extension_loaded('mbstring'),
            ],
            'safeModeOff' => [
                'title' => 'Safe mode is not enabled',
                'valid' => !ini_get('safe_mode'),
            ],
            'smtpCheck' => [
                'title' => 'PHP Mail SMTP is enabled',
                'valid' => strlen(ini_get('SMTP')) > 0,
            ],
            'intlCheck' => [
                'title' => 'Intl extension is enabled',
                'valid' => extension_loaded('intl'),
            ],
            'icuVersionCheck' => [
                'title' => 'ICU is installed and version is >=49.1',
                'valid' => defined('INTL_ICU_DATA_VERSION') && version_compare(INTL_ICU_DATA_VERSION, '49.1', '>='),
            ],
        ];
    }
}
