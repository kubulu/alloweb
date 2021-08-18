<?php

namespace app\notifications;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
abstract class BaseNotificationCategory extends BaseObject
{
    /**
     * @var string the category id
     */
    public $id;
    /**
     * @var int
     */
    public $sortOrder = 1000;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!$this->id) {
            throw new InvalidConfigException('"id" attribute must be defined');
        }
    }

    /**
     * @return string
     */
    abstract public function getTitle();

    /**
     * @return string
     */
    abstract public function getDescription();
}
