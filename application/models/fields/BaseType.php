<?php

namespace app\models\fields;

use app\helpers\Html;
use app\models\ProfileField;
use Yii;
use yii\base\Event;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\fields
 */
class BaseType extends Model
{
    const EVENT_FIELD_TYPES = 'fieldTypes';

    /**
     * @var BaseType[]
     */
    public $fieldTypes = [];

    /**
     * @var null|ProfileField
     */
    public $profileField = null;
    /**
     * @var boolean
     */
    public $required;

    /**
     * @param $profileField ProfileField
     */
    public function setProfileField($profileField)
    {
        $this->profileField = $profileField;
    }

    /**
     * @param $className
     * @param $title
     */
    public function addFieldType($className, $title)
    {
        $this->fieldTypes[$className] = $title;
    }

    /**
     * @return array
     */
    public function getFieldTypes()
    {
        $fieldTypes = array_merge([
            Text::class => Yii::t('app', 'Text'),
            TextArea::class => Yii::t('app', 'Text Area'),
            Number::class => Yii::t('app', 'Number'),
            Select::class => Yii::t('app', 'Select List'),
            Checkbox::class => Yii::t('app', 'Checkbox'),
        ], $this->fieldTypes);

        $this->trigger(self::EVENT_FIELD_TYPES, new Event(['sender' => $this]));

        return $fieldTypes;
    }

    /**
     * @param $fieldClass
     * @param $profileField
     * @return BaseType|null
     */
    public function getFieldType($fieldClass, $profileField)
    {
        $fieldTypes = $this->getFieldTypes();
        if (isset($fieldTypes[$fieldClass])) {
            /** @var BaseType $instance */
            $instance = new $fieldClass;
            $instance->profileField = $profileField;
            if ($profileField->field_class == $fieldClass) {
                $instance->loadFieldConfig();
            }

            return $instance;
        }

        return null;
    }

    public function loadFieldConfig()
    {
        if (empty($this->profileField->field_config)) {
            return;
        }
        $config = Json::decode($this->profileField->field_config);
        if (is_array($config)) {
            foreach ($config as $key => $value) {
                if ($this->hasProperty($key)) {
                    $this->$key = $value;
                }
                if ($this->profileField->hasAttribute($key)) {
                    $this->profileField->$key = $value;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getFieldConfig()
    {
        $data = [];
        foreach ($this->attributes as $attributeName => $value) {
            if ($attributeName == 'profileField' || $attributeName == 'fieldTypes') {
                continue;
            }
            if ($this->profileField->hasAttribute($attributeName)) {
                $data[$attributeName] = $this->profileField->$attributeName;
            }
        }

        return JSON::encode($data);
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return [
            $this->profileField->alias => Yii::t(
                $this->profileField->language_category,
                $this->profileField->title
            )
        ];
    }

    /**
     * @return array
     */
    public function getFieldOptions()
    {
        return [
            'required' => [
                'type' => 'checkbox',
                'label' => Yii::t('app', 'Required'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFieldRules()
    {
        $rules = [];

        if (isset($this->required) && $this->required) {
            $rules[] = [$this->profileField->alias, 'required'];
        }

        return $rules;
    }

    /**
     * @param $form ActiveForm
     * @param $model ActiveRecord
     * @param $options array
     * @return mixed
     */
    public function getFieldInput($form, $model, $options = [])
    {
        return null;
    }

    /**
     * @param $value
     * @param bool $raw
     * @return string
     */
    public function formatValue($value, $raw = false)
    {
        return $raw ? $value : Html::encode($value);
    }
}
