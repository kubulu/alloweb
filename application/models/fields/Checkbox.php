<?php

namespace app\models\fields;

use Yii;
use yii\widgets\ActiveForm;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\fields
 */
class Checkbox extends BaseType
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @return array
     */
    public function getFieldOptions()
    {
        return array_merge(parent::getFieldOptions(), [

        ]);
    }

    /**
     * @return array
     */
    public function getFieldRules()
    {
        $rules = [];
        $rules[] = [$this->profileField->alias, 'in', 'range' => [0, 1]];
        $rules[] = [$this->profileField->alias, 'default', 'value' => $this->profileField];

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
        return $form->field($model, $this->profileField->alias)->checkbox($options);
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

        return $value == true ? Yii::t('app', 'Yes') : Yii::t('app', 'No');
    }
}
