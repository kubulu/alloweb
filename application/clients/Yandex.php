<?php

namespace app\clients;

use Yii;
use yii\authclient\clients\Yandex as BaseYandex;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
class Yandex extends BaseYandex implements ClientInterface
{
    /**
     * @return mixed|null|string
     */
    public function getEmail()
    {
        $emails = isset($this->getUserAttributes()['emails'])
            ? $this->getUserAttributes()['emails']
            : null;

        if ($emails !== null && isset($emails[0])) {
            return $emails[0];
        } else {
            return null;
        }
    }

    /**
     * @return mixed|null|string
     */
    public function getUsername()
    {
        return isset($this->getUserAttributes()['login'])
            ? $this->getUserAttributes()['login']
            : null;
    }

    /**
     * @return string
     */
    protected function defaultTitle()
    {
        return Yii::t('app', 'Yandex');
    }
}
