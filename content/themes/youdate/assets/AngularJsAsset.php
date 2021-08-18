<?php

namespace youdate\assets;

use yii\web\AssetBundle;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\assets
 */
class AngularJsAsset extends AssetBundle
{
    public $sourcePath = '@bower/angularjs';
    public $css = [
        'angular-csp.css',
    ];
    public $js = [
        'angular.min.js',
    ];
    public $depends = [
        CoreAsset::class,
    ];
}
