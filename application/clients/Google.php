<?php

namespace app\clients;

use yii\authclient\clients\Google as BaseGoogle;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
class Google extends BaseGoogle implements ClientInterface
{
    /**
     * @return mixed|null|string
     */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['emails'][0]['value'])
            ? $this->getUserAttributes()['emails'][0]['value']
            : null;
    }

    /**
     * @return mixed|null|string|void
     */
    public function getUsername()
    {
        return;
    }
}
