<?php

namespace app\settings;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\settings
 */
class UserSettings extends Settings
{
    /**
     * @var integer
     */
    protected $userId;

    /**
     * @param $userId
     * @return UserSettings
     */
    public static function forUser($userId)
    {
        $settings = new static();
        $settings->userId = $userId;

        return $settings;
    }

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     * @throws \Exception
     */
    public function getUserSetting($key, $default = null)
    {
        return parent::get("user.{$this->userId}", $key, $default);
    }

    /**
     * @param $key
     * @param $value
     * @throws \yii\db\Exception
     */
    public function setUserSetting($key, $value)
    {
        return parent::set("user.{$this->userId}", $key, $value);
    }

    /**
     * @param $key
     * @throws \yii\db\Exception
     */
    public function removeUserSetting($key)
    {
        return parent::remove("user.{$this->userId}", $key);
    }
}
