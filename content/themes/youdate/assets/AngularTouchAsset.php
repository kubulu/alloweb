<?php

namespace youdate\assets;

use yii\web\AssetBundle;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\assets
 */
class AngularTouchAsset extends AssetBundle
{
    public $sourcePath = '@bower/angular-touch';
    public $css = [
    ];
    public $js = [
        'angular-touch.min.js',
    ];
    public $depends = [
        AngularJsAsset::class,
    ];
}
