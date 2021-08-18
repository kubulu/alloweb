<?php

namespace mydate\components;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package mydate\components
 */
class ThemeSettings extends \youdate\components\ThemeSettings
{
    /**
     * @return array
     */
    public function getSettings()
    {
        $settings = [
            // your custom settings
            // see `content/themes/youdate/components/ThemeSettings.php
            // ...
        ];

        return array_merge($settings, parent::getSettings());
    }
}
