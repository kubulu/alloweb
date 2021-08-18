<?php

namespace youdate\widgets;

use yii\helpers\Json;
use yii\jui\JuiAsset;
use youdate\assets\UploadAsset;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class Upload extends \trntv\filekit\widget\Upload
{
    public function registerClientScript()
    {
        UploadAsset::register($this->getView());
        $options = Json::encode($this->clientOptions);
        if ($this->sortable) {
            JuiAsset::register($this->getView());
        }
        $this->getView()->registerJs("jQuery('#{$this->getId()}').yiiUploadKit({$options});");
    }
}
