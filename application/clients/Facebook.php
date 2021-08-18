<?php

namespace app\clients;

use yii\authclient\clients\Facebook as BaseFacebook;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
class Facebook extends BaseFacebook implements ClientInterface
{
    /** @inheritdoc */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['email'])
            ? $this->getUserAttributes()['email']
            : null;
    }

    /** @inheritdoc */
    public function getUsername()
    {
        return;
    }

    /**
     * @return mixed|string
     */
    public function getReturnUrl()
    {
        $redirectUrl = env('SOCIAL_FACEBOOK_REDIRECT_URL', false);
        if (!empty($redirectUrl) && $redirectUrl !== false) {
            return $redirectUrl;
        }

        return parent::getReturnUrl();
    }
}
