<?php

namespace app\models\fields;

use app\helpers\Html;
use Yii;
use youdate\widgets\ActiveForm;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\fields
 */
class Number extends BaseType
{
    /**
     * @var bool
     */
    public $integerOnly;
    /**
     * @var string|float|integer
     */
    public $maxValue;

    /**
     * @var string|float|integer
     */
    public $minValue;
    /**
     * @var string
     */
    public $prefix;
    /**
     * @var string
     */
    public $postfix;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['maxValue', 'minValue'], 'number'],
            [['prefix', 'postfix'], 'string', 'max' => 255],
            [['integerOnly'], 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function getFieldOptions()
    {
        return array_merge(parent::getFieldOptions(), [
            'integerOnly' => [
                'type' => 'checkbox',
                'label' => Yii::t('app', 'Integers only'),
            ],
            'minValue' => [
                'type' => 'text',
                'label' => Yii::t('app', 'Minimum value'),
            ],
            'maxValue' => [
                'type' => 'text',
                'label' => Yii::t('app', 'Maximum value'),
            ],
            'prefix' => [
                'type' => 'text',
                'label' => Yii::t('app', 'Prefix text'),
            ],
            'postfix' => [
                'type' => 'text',
                'label' => Yii::t('app', 'Postfix text'),
            ],
        ]);
    }

    /**
     * @return array
     */
    public function getFieldRules()
    {
        $rules = [];
        $rules[] = [$this->profileField->alias, 'number', 'integerOnly' => $this->integerOnly];

        if ($this->maxValue) {
            $rules[] = [$this->profileField->alias, 'number', 'max' => $this->maxValue];
        }

        if ($this->minValue) {
            $rules[] = [$this->profileField->alias, 'number', 'min' => $this->minValue];
        }

        return array_merge(parent::getFieldRules(), $rules);
    }

    /**
     * @param ActiveForm $form
     * @param $model
     * @param array $options
     * @return mixed
     */
    public function getFieldInput($form, $model, $options = [])
    {
        return $form->field($model, $this->profileField->alias)->textInput($options);
    }

    /**
     * @param $value
     * @param bool $raw
     * @return string
     */
    public function formatValue($value, $raw = false)
    {
        if ($raw) {
            return $value;
        }

        return Html::encode(sprintf("%s %s %s", $this->prefix, $value, $this->postfix));
    }
}
