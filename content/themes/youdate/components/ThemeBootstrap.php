<?php

namespace youdate\components;

use Yii;
use yii\base\BootstrapInterface;
use youdate\assets\UploadAsset;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\components
 */
class ThemeBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->assetManager->bundles[UploadAsset::class] = [
            'sourcePath' => Yii::getAlias('@theme/static'),
            'css' => [],
            'js' => [
                'js/vendors/filekit.js',
            ]
        ];
    }
}
