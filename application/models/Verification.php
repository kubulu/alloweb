<?php

namespace app\models;

use app\models\query\VerificationQuery;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property int $user_id
 * @property string $verification_photo
 * @property bool $is_viewed
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class Verification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%verification}}';
    }

    /**
     * @inheritdoc
     * @return VerificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VerificationQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
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
            [['verification_photo'], 'string', 'max' => 500],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['is_viewed'], 'boolean'],
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
            'user_id' => Yii::t('app', 'User'),
            'verification_photo' => Yii::t('app', 'Verification Photo'),
            'is_viewed' => Yii::t('app', 'Viewed'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
     * @return mixed
     */
    public function getUrl()
    {
        return Yii::$app->photoStorage->getUrl($this->verification_photo);
    }

    /**
     * @param $width
     * @param $height
     * @param string $fit
     * @return mixed
     */
    public function getVerificationPhotoUrl($width, $height, $fit = 'crop-center')
    {
        return Yii::$app->glide->createSignedUrl([
            env('ADMIN_PREFIX') . '/verification/thumbnail', 'id' => $this->id,
            'w' => $width, 'h' => $height, 'sharp' => 1, 'fit' => $fit,
        ], true);
    }
}
