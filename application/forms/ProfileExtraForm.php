<?php

namespace app\forms;

use app\models\ProfileExtra;
use app\models\ProfileField;
use yii\base\DynamicModel;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class ProfileExtraForm extends DynamicModel
{
    /**
     * @var string
     */
    protected $categoryAlias;
    /**
     * @var array
     */
    protected $rules;

    /**
     * @param ProfileField[] $fields
     * @param ProfileExtra[] $values
     * @param $categoryAlias
     * @return ProfileExtraForm
     * @throws \yii\base\InvalidConfigException
     */
    public static function createFromFields($fields, $values, $categoryAlias)
    {
        $attributes = [];
        $rules = [];

        foreach ($fields as $field) {
            $fieldInstance = $field->getFieldInstance();
            if ($fieldInstance) {
                $rules = array_merge($rules, $field->getFieldInstance()->getFieldRules());
                $attributes[] = $field->alias;
            }
        }

        $model = new static($attributes);
        $model->categoryAlias = $categoryAlias;
        $model->rules = $rules;

        foreach ($values as $value) {
            if (in_array($value->field->alias, $attributes)) {
                $model->{$value->field->alias} = $value->value;
            }
        }

        return $model;
    }

    public function rules()
    {
        return $this->rules;
    }

    /**
     * @return string
     */
    public function formName()
    {
        return $this->categoryAlias;
    }
}
