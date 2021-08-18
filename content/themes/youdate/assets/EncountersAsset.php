<?php

namespace youdate\assets;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\assets
 */
class EncountersAsset extends \yii\web\AssetBundle
{
    public $basePath = '@theme/static';
    public $baseUrl = '@themeUrl/static';
    public $js = [
        'js/vendors/ui-bootstrap-tpls-3.0.5.min.js',
        'js/encounters.js',
    ];
    public $depends = [
        AngularJsAsset::class,
        AngularTouchAsset::class,
    ];
}
