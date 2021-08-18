<?php

namespace app\modules\admin\assets;

use yii\web\AssetBundle;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\assets
 */
class AdminAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/admin/static';
    public $css = [
        'css/messenger.css',
        'css/messenger-theme-flat.css',
        'css/jquery.fancybox.min.css',
        'css/admin.min.css',
    ];
    public $js = [
        'js/jquery.slimscroll.min.js',
        'js/bootbox.min.js',
        'js/messenger.min.js',
        'js/messenger-theme-flat.js',
        'js/jquery.fancybox.min.js',
        'js/adminlte.min.js',
        'js/admin.js',
        'js/translate.js',
    ];
    public $depends = [
        \yii\bootstrap\BootstrapAsset::class,
        \yii\bootstrap\BootstrapPluginAsset::class,
        \yii\web\YiiAsset::class,
        \rmrevin\yii\fontawesome\AssetBundle::class,
    ];
}
