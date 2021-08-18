<?php

namespace app\models\fields;

use app\helpers\Html;
use Yii;
use yii\widgets\ActiveForm;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\fields
 */
class Select extends BaseType
{
    /**
     * @var string
     */
    public $options;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['options'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function getFieldOptions()
    {
        return array_merge(parent::getFieldOptions(), [
            'options' => [
                'type' => 'textarea',
                'label' => Yii::t('app', 'Values'),
                'hint' => Yii::t('app', 'Every {0} on each line', '<code>value => title</code>'),
            ],
        ]);
    }

    /**
     * @return array
     */
    public function getFieldRules()
    {
        $rules = [];
        $rules[] = [$this->profileField->alias, 'in', 'range' => array_keys($this->getSelectItems())];

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
        $options['prompt'] = Yii::t('app', '-- Select --');
        return $form->field($model, $this->profileField->alias)->dropDownList($this->getSelectItems(), $options);
    }

    /**
     * @return array
     */
    public function getSelectItems()
    {
        $items = [];

        foreach (explode("\n", $this->options) as $option) {
            if (strpos($option, '=>') !== false) {
                list($key, $value) = explode('=>', $option);
                $items[trim($key)] = Yii::t($this->profileField->language_category, trim($value));
            } else {
                $items[] = Yii::t($this->profileField->language_category, trim($option));
            }
        }

        return $items;
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

        $items = $this->getSelectItems();

        return Html::encode(isset($items[$value]) ? $items[$value] : $value);
    }
}
