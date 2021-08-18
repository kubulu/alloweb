<?php

namespace youdate\assets;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\assets
 */
class PaymentAsset extends \yii\web\AssetBundle
{
    public $basePath = '@theme/static';
    public $baseUrl = '@themeUrl/static';
    public $js = [
        '//checkout.stripe.com/checkout.js',
        'js/payment.js',
    ];
    public $depends = [
        Asset::class,
    ];
}
