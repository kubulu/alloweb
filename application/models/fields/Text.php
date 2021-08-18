<?php

namespace app\models\fields;

use Yii;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\fields
 */
class Text extends BaseType
{
    const VALIDATOR_EMAIL = 'email';
    const VALIDATOR_URL = 'url';

    /**
     * @var
     */
    public $validator;
    /**
     * @var
     */
    public $minLength;
    /**
     * @var int
     */
    public $maxLength = 255;
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
            [['validator'], 'in', 'range' => [self::VALIDATOR_EMAIL, self::VALIDATOR_URL]],
            [['maxLength', 'minLength'], 'integer', 'min' => 1, 'max' => 255],
            [['prefix', 'postfix'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function getFieldOptions()
    {
        return array_merge(parent::getFieldOptions(), [
            'validator' => [
                'type' => 'dropdown',
                'label' => Yii::t('app', 'Validator'),
                'items' => [
                    self::VALIDATOR_URL => Yii::t('app', 'URL'),
                    self::VALIDATOR_EMAIL => Yii::t('app', 'E-mail'),
                ],
            ],
            'minLength' => [
                'type' => 'text',
                'label' => Yii::t('app', 'Minimum length'),
            ],
            'maxLength' => [
                'type' => 'text',
                'label' => Yii::t('app', 'Maximum length'),
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
        if (empty($this->maxLength) || $this->maxLength > 255) {
            $rules[] = [$this->profileField->alias, 'string', 'max' => 255];
        } else {
            $rules[] = [$this->profileField->alias, 'string', 'max' => $this->maxLength];
        }

        if (!empty($this->minLength)) {
            $rules[] = [$this->profileField->alias, 'string', 'min' => $this->minLength];
        }

        if (!empty($this->validator)) {
            $rules[] = [$this->profileField->alias, $this->validator];
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

        if ($this->validator == self::VALIDATOR_URL) {
            return Html::a($value, $value);
        }

        if ($this->validator == self::VALIDATOR_EMAIL) {
            return Html::mailto($value, $value);
        }

        return Html::encode(sprintf("%s %s %s", $this->prefix, $value, $this->postfix));
    }
}
