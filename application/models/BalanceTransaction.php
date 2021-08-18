<?php

namespace app\models;

use app\payments\TransactionInfo;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property string $data
 * @property int $created_at
 *
 * @property User $user
 */
class BalanceTransaction extends \yii\db\ActiveRecord
{
    /**
     * @var TransactionInfo
     */
    protected $transactionInfo;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%balance_transaction}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'amount', 'created_at'], 'integer'],
            [['data'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app', 'User ID'),
            'amount' => Yii::t('app', 'Amount'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return TransactionInfo|null
     */
    public function getTransactionInfo()
    {
        if (isset($this->transactionInfo)) {
            return $this->transactionInfo;
        }

        try {
            $data = json_decode($this->data, true);
        } catch (\Exception $e) {
            return null;
        }

        if (isset($data['class']) && class_exists($data['class'])) {
            /** @var TransactionInfo $infoClass */
            $this->transactionInfo = new $data['class'];
            $this->transactionInfo->setData($data);
            return $this->transactionInfo;
        }

        return null;
    }
}
