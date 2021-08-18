<?php

namespace app\forms;

use app\managers\BalanceManager;
use app\models\Photo;
use app\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class SpotlightForm extends \yii\base\Model
{
    /**
     * @var string
     */
    public $message;
    /**
     * @var integer
     */
    public $userId;
    /**
     * @var integer
     */
    public $photoId;

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['photoId', 'userId'], 'required'],
            [['userId', 'photoId'], 'integer'],
            ['userId', 'validateUserBalance'],
            ['message', 'string', 'max' => 50],
            [['userId'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => ['userId' => 'id']
            ],
            [['photoId'], 'exist',
                'targetClass' => Photo::class,
                'targetAttribute' => ['photoId' => 'id'],
                'filter' => function(ActiveQuery $query) {
                    $query->andWhere(['photo.user_id' => $this->userId]);
                }
            ],
        ];

        return $rules;
    }

    /**
     * @return bool
     */
    public function validateUserBalance()
    {
        /** @var BalanceManager $balanceManager */
        $balanceManager = Yii::$app->balanceManager;
        if (!$balanceManager->hasEnoughCredits($this->userId, $balanceManager->getSpotlightPrice())) {
            $this->addError('userId', Yii::t('app', 'You don\'t have enough credits for this operation'));
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'userId' => Yii::t('app', 'User'),
            'photoId' => Yii::t('app', 'Photo'),
            'message' => Yii::t('app', 'Message'),
        ];
    }
}
