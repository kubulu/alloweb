<?php

namespace app\settings;

use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\Json;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\settings
 */
class Settings extends Component
{
    /**
     * @var string DB component ID
     */
    public $db = 'db';
    /**
     * @var array Categories to be pre-loaded on init
     */
    public $preLoad = [];
    /**
     * @var array Loaded settings
     */
    protected $items = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!empty($this->preLoad)) {
            $this->load($this->preLoad);
        }
    }

    /**
     * Returns settings.
     *
     * @param string $category Category name
     * @param string|array $key Single or multiple keys in array
     * @param mixed $default Default value when setting does not exist
     * @return mixed Setting value
     * @throws \Exception
     */
    public function get($category, $key = null, $default = null)
    {
        $this->load($category);
        if ($key === null) {
            return isset($this->items[$category]) && !empty($this->items[$category]) ? $this->items[$category] : $default;
        }
        if (is_string($key)) {
            return isset($this->items[$category][$key]) ? $this->items[$category][$key] : $default;
        }
        if (is_array($key)) {
            $result = [];
            foreach ($key as $val) {
                $result[$val] = isset($this->items[$category][$val]) ? $this->items[$category][$val] : (is_array($default) && isset($default[$val]) ? $default[$val] : null);
            }
            return $result;
        }
        return $default;
    }

    /**
     * Saves settings
     *
     * @param string $category Category name
     * @param mixed $key Setting key or array of settings ie.: ['key' => value'', 'key2' => 'value2']
     * @param mixed $value Setting value
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function set($category, $key, $value = null)
    {
        if (is_string($key)) {
            $encodedVal = Json::encode($value);
            $this->get($category, $key, null);
            if (array_key_exists($key, $this->items[$category])) {
                $this->dbUpdate($category, $key, $encodedVal);
            } else {
                $this->dbInsert($category, $key, $encodedVal);
            }
            $this->items[$category][$key] = $value;
        }
        if (is_array($key)) {
            foreach ($key as $idx => $val) {
                $this->set($category, $idx, $val);
            }
        }
    }

    /**
     * Removes settings
     *
     * @param string $category Category name
     * @param array|string|null $key Setting key, keys array or null to delete all settings from category
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function remove($category, $key = null)
    {
        if ($key === null) {
            $this->dbDelete($category);
            if (isset($this->items[$category])) {
                unset($this->items[$category]);
            }
        }
        if (is_string($key)) {
            $this->dbDelete($category, $key);
            if (isset($this->items[$category][$key])) {
                unset($this->items[$category][$key]);
            }
        }
        if (is_array($key)) {
            foreach ($key as $idx => $val) {
                $this->remove($category, $val);
            }
        }
    }

    /**
     * Loads settings from category or multiple categories
     * @param array|string $categories Category name
     */
    public function load($categories)
    {
        if (is_string($categories)) {
            $categories = [$categories];
        }
        foreach ((array)$categories as $idx => $category) {
            if (isset($this->items[$category])) {
                unset($categories[$idx]);
            } else {
                $this->items[$category] = [];
            }
        }
        if (empty($categories)) {
            return;
        }
        $result = (new Query())->select(['category', 'key', 'value'])->from('setting')->where([
            'category' => $categories
        ])->all();
        foreach ($result as $row) {
            try {
                $this->items[$row['category']][$row['key']] = Json::decode($row['value']);
            } catch (InvalidArgumentException $ex) {
                $this->items[$row['category']][$row['key']] = $row['value'];
            }
        }
    }

    /**
     * @return Connection Database connection
     */
    protected function getDb()
    {
        return \Yii::$app->{$this->db};
    }

    /**
     * Stores settings in database
     *
     * @param string $category Category name
     * @param string $key Setting key
     * @param string $value Setting value
     * @throws \yii\db\Exception
     */
    protected function dbInsert($category, $key, $value)
    {
        $this->getDb()->createCommand('
        INSERT INTO
            {{setting}} (`category`, `key`, `value`, `created_at`)
        VALUES
            (:category, :key, :value, UNIX_TIMESTAMP())
        ', compact('category', 'key', 'value'))->execute();
    }

    /**
     * Updates setting in database
     *
     * @param string $category Category name
     * @param string $key Setting key
     * @param string $value Setting value
     * @throws \yii\db\Exception
     */
    protected function dbUpdate($category, $key, $value)
    {
        $this->getDb()->createCommand('
            UPDATE
                {{setting}}
            SET
                `value` = :value,
                `updated_at` = UNIX_TIMESTAMP()
            WHERE
                category = :category
                AND `key` = :key
        ', compact('category', 'key', 'value'))->execute();
    }

    /**
     * Deletes setting from database
     *
     * @param string|array $category Category name(s)
     * @param string|array|null $key Setting key(s) or null to delete all values from category
     * @throws \yii\db\Exception
     */
    protected function dbDelete($category, $key = null)
    {
        if ($key === null) {
            $this->getDb()->createCommand('
                DELETE FROM
                    {{setting}}
                WHERE
                    `category` = :category
            ', compact('category'))->execute();
        }
        if (is_string($key)) {
            $this->getDb()->createCommand('
                    DELETE FROM
                        {{setting}}
                    WHERE
                        `category` = :category
                        AND `key` = :key
                ', compact('category', 'key'))->execute();
        }
        if (is_array($key)) {
            foreach ($key as $val) {
                $this->dbDelete($category, $val);
            }
        }
    }
}
