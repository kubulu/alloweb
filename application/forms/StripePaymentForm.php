<?php

namespace app\forms;

use yii\base\Model;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class StripePaymentForm extends Model
{
    /**
     * @var string
     */
    public $stripeToken;
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
            [['stripeToken', 'credits'], 'required'],
            ['stripeToken', 'string'],
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
