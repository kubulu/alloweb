<?php

namespace app\settings;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\settings
 *
 * @property array getFormConfig()
 */
class SettingsManager
{
    /**
     * @var string
     */
    protected $category;
    /**
     * @var array
     */
    protected $items = [];

    /**
     * SettingsManager constructor.
     * @param $category
     * @param $items
     */
    public function __construct($category, $items)
    {
        $this->category = $category;
        $this->items = $items;
    }

    /*
     * Get settings category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get settings items (fields, types etc.)
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return Settings Settings component
     */
    public function getSettingsComponent()
    {
        return \Yii::$app->settings;
    }

    /**
     * Returns settings from current category.
     *
     * @param string|array $key Single or multiple keys in array
     * @param mixed $default Default value when setting does not exist
     * @return mixed Setting value
     * @throws \Exception
     */
    public function getSetting($key = null, $default = null)
    {
        return $this->getSettingsComponent()->get($this->category, $key, $default);
    }

    /**
     * Saves setting
     *
     * @param mixed $key Setting key or array of settings ie.: ['key' => value'', 'key2' => 'value2']
     * @param mixed $value Setting value
     * @throws \Exception
     */
    public function setSetting($key, $value = null)
    {
        $this->getSettingsComponent()->set($this->category, $key, $value);
    }

    /**
     * Removes setting
     *
     * @param array|string|null $key Setting key, keys array or null to delete all settings from category
     * @throws \Exception
     */
    public function removeSetting($key = null)
    {
        $this->getSettingsComponent()->remove($this->category, $key);
    }
}
