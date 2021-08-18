<?php

namespace app\clients;

use Yii;
use yii\authclient\clients\VKontakte as BaseVKontakte;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
class VK extends BaseVKontakte implements ClientInterface
{
    /**
     * @var string
     */
    public $scope = 'email';

    /**
     * @return mixed|null|string
     */
    public function getEmail()
    {
        return $this->getAccessToken()->getParam('email');
    }

    /**
     * @return mixed|null|string
     */
    public function getUsername()
    {
        return isset($this->getUserAttributes()['screen_name'])
            ? $this->getUserAttributes()['screen_name']
            : null;
    }

    /**
     * @return string
     */
    protected function defaultTitle()
    {
        return Yii::t('app', 'VK');
    }
}
