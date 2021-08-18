<?php

namespace app\models;

use app\models\query\ReportQuery;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property int $from_user_id
 * @property int $reported_user_id
 * @property int $is_viewed
 * @property string $reason
 * @property string $description
 * @property int $created_at
 *
 * @property User $fromUser
 * @property User $reportedUser
 */
class Report extends \yii\db\ActiveRecord
{
    const REASON_SPAM = 'spam';
    const REASON_BAD_PROFILE = 'profile';
    const REASON_RUDE = 'rude';
    const REASON_FAKE = 'fake';
    const REASON_SCAM = 'scam';
    const REASON_OTHER = 'other';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%report}}';
    }

    /**
     * @inheritdoc
     * @return ReportQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReportQuery(get_called_class());
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
            [['from_user_id', 'reported_user_id', 'reason'], 'required'],
            [['from_user_id', 'reported_user_id', 'is_viewed', 'created_at'], 'integer'],
            [['reason'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 200],
            [['description'], 'required', 'when' => function(Report $model) {
                return $model->reason == self::REASON_OTHER;
            }],
            [['from_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['from_user_id' => 'id']
            ],
            [['reported_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['reported_user_id' => 'id']
            ],
            ['from_user_id', 'compare', 'compareAttribute' => 'reported_user_id', 'operator' => '!='],
            ['reason', 'in', 'range' => [
                self::REASON_SPAM, self::REASON_BAD_PROFILE,
                self::REASON_RUDE, self::REASON_FAKE,
                self::REASON_SCAM, self::REASON_OTHER,
            ]],
            [['from_user_id', 'reported_user_id', 'reason'], 'unique',
                'targetAttribute' => ['from_user_id', 'reported_user_id', 'reason']
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_user_id' => Yii::t('app', 'From User'),
            'reported_user_id' => Yii::t('app', 'Reported User'),
            'is_viewed' => Yii::t('app', 'Viewed'),
            'reason' => Yii::t('app', 'Reason'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportedUser()
    {
        return $this->hasOne(User::class, ['id' => 'reported_user_id']);
    }

    /**
     * @return array
     */
    public function getReportReasons()
    {
        return [
            self::REASON_SPAM => Yii::t('app', 'Spammer'),
            self::REASON_BAD_PROFILE => Yii::t('app', 'Profile content'),
            self::REASON_RUDE => Yii::t('app', 'Rude behavior'),
            self::REASON_FAKE => Yii::t('app', 'Fake profile'),
            self::REASON_SCAM => Yii::t('app', 'Scammer'),
            self::REASON_OTHER => Yii::t('app', 'Other'),
        ];
    }
}
