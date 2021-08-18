<?php

namespace app\events;

use app\models\User;
use app\base\Event;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\events
 * @property User $user
 */
class FromToUserEvent extends Event
{
    /**
     * @var User
     */
    public $fromUser;
    /**
     * @var User
     */
    public $toUser;
    /**
     * @var object|null
     */
    public $relatedData;
}
