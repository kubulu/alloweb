<?php

namespace app\events;

use app\models\User;
use app\base\Event;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\events
 * @property User $user
 */
class UserEvent extends Event
{
    /**
     * @var User
     */
    private $_user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }
}
