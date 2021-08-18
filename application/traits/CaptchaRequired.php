<?php

namespace app\traits;

use Yii;

/**
 * @package app\traits
 */
trait CaptchaRequired
{
    /**
     * @return bool
     */
    public function isCaptchaRequired()
    {
        return Yii::$app->settings->get('frontend', 'siteRequireCaptcha', false);
    }
}
