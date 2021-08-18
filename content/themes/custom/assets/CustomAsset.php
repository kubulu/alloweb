<?php

namespace custom\assets;

use yii\web\AssetBundle;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package custom\assets
 */
class CustomAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@extendedTheme/static';
    /**
     * @var string
     */
    public $baseUrl = '@extendedThemeUrl/static';
    /**
     * @var array
     */
    public $css = ['css/custom.css'];
}
