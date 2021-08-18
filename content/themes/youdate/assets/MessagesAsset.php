<?php

namespace youdate\assets;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\assets
 */
class MessagesAsset extends \yii\web\AssetBundle
{
    public $basePath = '@theme/static';
    public $baseUrl = '@themeUrl/static';
    public $js = [
        'js/vendors/scrollglue.js',
        'js/vendors/angular-lazy-img.min.js',
        'js/vendors/ngBootbox.min.js',
        'js/vendors/ui-bootstrap-tpls-3.0.5.min.js',
        'js/messages.js',
    ];
    public $depends = [
        AngularJsAsset::class,
    ];
}
