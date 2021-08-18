<?php

namespace youdate\assets;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\assets
 */
class CoreAsset extends \yii\web\AssetBundle
{
    public $basePath = '@theme/static';
    public $baseUrl = '@themeUrl/static';
    public $css = [
        'css/messenger.css',
        'css/messenger-theme-flat.css',
    ];
    public $js = [
        'js/vendors/bootstrap.bundle.min.js',
        'js/vendors/bootbox.min.js',
        'js/vendors/js.cookie-2.2.0.min.js',
        'js/vendors/messenger.min.js',
        'js/vendors/messenger-theme-flat.js',
        'js/vendors/selectize.min.js',
        'js/vendors/nprogress.js',
        'js/tabler.js',
    ];
    public $depends = [
        \yii\web\JqueryAsset::class,
        \yii\web\YiiAsset::class,
    ];
}
