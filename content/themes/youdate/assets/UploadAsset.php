<?php

namespace youdate\assets;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\assets
 */
class UploadAsset extends \trntv\filekit\widget\UploadAsset
{
    public $depends = [
        'yii\web\JqueryAsset',
        'trntv\filekit\widget\BlueimpFileuploadAsset'
    ];
}
