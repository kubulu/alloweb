<?php

namespace app\modules\admin\forms;

use Yii;
use yii\base\Model;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\forms
 */
class BalanceUpdateForm extends Model
{
    /**
     * @var integer
     */
    public $amount;
    /**
     * @var string
     */
    public $notes;

    public function init()
    {
        parent::init();
        if (!isset($this->amount)) {
            $this->amount = 100;
        }
        if (!isset($this->notes)) {
            $this->notes = Yii::t('app', 'Credits given by administration');
        }
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['amount', 'notes'], 'required'],
            [['amount'], 'integer', 'min' => 1, 'max' => 1000000],
            [['notes'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'amount' => Yii::t('app', 'Amount'),
            'notes' => Yii::t('app', 'Notes'),
        ];
    }
}
