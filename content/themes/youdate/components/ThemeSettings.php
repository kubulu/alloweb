<?php

namespace youdate\components;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\components
 */
class ThemeSettings extends \app\themes\ThemeSettings
{
    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'alias' => 'logoUrl',
                'type' => 'text',
                'label' => Yii::t('youdate', 'Logo URL'),
                'help' => Yii::t('youdate', 'Relative: {relative} or absolute {absolute}', [
                    'relative' => '<code>@themeUrl/static/images/logo@2x.png</code>',
                    'absolute' => '<code>https://youdate-demo.hauntd.me/content/custom-logo.png</code>',
                ]),
                'rules' => [
                    ['default', 'value' => '@themeUrl/static/images/logo@2x.png'],
                    ['string', 'max' => 255],
                ]
            ],
            [
                'alias' => 'homepageTitle',
                'type' => 'text',
                'label' => Yii::t('youdate', 'Homepage title'),
                'rules' => [
                    ['default', 'value' => 'Welcome to YouDate!'],
                    ['string', 'min' => 2, 'max' => 255],
                ]
            ],
            [
                'alias' => 'homepageSubTitle',
                'type' => 'text',
                'label' => Yii::t('youdate', 'Homepage sub-title'),
                'rules' => [
                    ['default', 'value' => 'YouDate is a place where you can find your love or friends.'],
                    ['string', 'min' => 2, 'max' => 255],
                ],
            ],
            [
                'alias' => 'linksFacebook',
                'type' => 'text',
                'label' => Yii::t('youdate', 'Facebook link'),
                'rules' => [
                    ['string', 'min' => 2, 'max' => 255],
                ],
            ],
            [
                'alias' => 'linksTwitter',
                'type' => 'text',
                'label' => Yii::t('youdate', 'Twitter link'),
                'rules' => [
                    ['string', 'min' => 2, 'max' => 255],
                ],
            ],
            [
                'alias' => 'adsSidebar',
                'type' => 'code',
                'label' => Yii::t('youdate', 'Ads code (sidebar)'),
                'rules' => [
                    ['string', 'max' => 4000],
                ],
            ],
            [
                'alias' => 'adsHeader',
                'type' => 'code',
                'label' => Yii::t('youdate', 'Ads code (header)'),
                'rules' => [
                    ['string', 'max' => 4000],
                ],
            ],
        ];
    }
}
