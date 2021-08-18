<?php

namespace custom\components;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package custom\components
 */
class ThemeSettings extends \youdate\components\ThemeSettings
{
    /**
     * @return array
     */
    public function getSettings()
    {
        $settings = [
            [
                'alias' => 'pinkEnabled',
                'type' => 'checkbox',
                'label' => Yii::t('custom', 'Make website pink'),
                'rules' => [
                    ['default', 'value' => false],
                    ['boolean'],
                ]
            ],
        ];

        return array_merge($settings, parent::getSettings());
    }
}
