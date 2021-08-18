<?php

namespace app\forms;

use yii\base\Model;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class PaypalPaymentForm extends Model
{
    /**
     * @var integer
     */
    public $credits;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['credits'], 'required'],
            ['credits', 'integer', 'min' => 50, 'max' => 1000],
        ];
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }
}
