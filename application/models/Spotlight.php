<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property int $user_id
 * @property int $photo_id
 * @property string $message
 * @property int $created_at
 *
 * @property Photo $photo
 * @property User $user
 */
class Spotlight extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%spotlight}}';
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
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'photo_id'], 'required'],
            [['user_id', 'photo_id', 'created_at'], 'integer'],
            [['message'], 'string', 'max' => 255],
            [['photo_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Photo::class,
                'targetAttribute' => ['photo_id' => 'id']
            ],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
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
            'user_id' => Yii::t('app', 'User'),
            'photo_id' => Yii::t('app', 'Photo'),
            'message' => Yii::t('app', 'Message'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasOne(Photo::class, ['id' => 'photo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return query\SpotlightQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new \app\models\query\SpotlightQuery(get_called_class());
    }
}
