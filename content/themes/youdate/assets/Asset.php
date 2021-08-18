<?php

namespace youdate\assets;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\assets
 */
class Asset extends \yii\web\AssetBundle
{
    public $basePath = '@theme/static';
    public $baseUrl = '@themeUrl/static';
    public $css = [
        'css/app.min.css',
    ];
    public $js = [
        'js/app.js',
    ];
    public $depends = [
        CoreAsset::class,
    ];
}
