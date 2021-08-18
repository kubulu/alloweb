<?php

namespace  youdate\assets;

use yii\web\AssetBundle;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\assets
 */
class WebCamJsAsset extends AssetBundle
{
    public $basePath = '@theme/static';
    public $baseUrl = '@themeUrl/static';
    public $js = [
        'js/vendors/webcam.js',
    ];
}
