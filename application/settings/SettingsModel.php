<?php

namespace app\settings;

use yii\base\DynamicModel;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\yii2settings
 */
class SettingsModel extends DynamicModel
{
    /**
     * @var array
     */
    public $items = [];
    /**
     * @var array
     */
    public $itemsLabels = [];
    /**
     * @var array
     */
    public $itemsRules = [];

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'Settings';
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return $this->itemsLabels;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return $this->itemsRules;
    }

    /**
     * @param array $items
     * @return SettingsModel
     */
    public static function createModel($items = [])
    {
        $attributes = [];
        $rules = [];
        $labels = [];

        foreach ($items as $item) {
            $attributes[$item['alias']] = $item['alias'];
            $rules[$item['alias']] = isset($item['rules']) ? $item['rules'] : null;
            $labels[$item['alias']] = isset($item['label']) ? $item['label'] : null;
        }
        $model = new self($attributes);
        $model->items = $items;
        foreach ($attributes as $attribute) {
            if ($rules[$attribute] !== null) {
                foreach ($rules[$attribute] as $rule) {
                    $model->itemsRules[] = array_merge([$attribute], $rule);
                }
            }
            $model->itemsLabels[$attribute] = isset($labels[$attribute]) ? $labels[$attribute] : $model->generateAttributeLabel($attribute);
        }
        return $model;
    }
}
