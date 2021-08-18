<?php

namespace app\models;

use app\behaviors\PolymorphicRelation;
use app\models\query\NotificationQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property string $class
 * @property int $user_id
 * @property int $sender_user_id
 * @property int $is_viewed
 * @property string $source_class
 * @property int $source_pk
 * @property int $created_at
 *
 * @property User $sender
 * @property User $user
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @return NotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'polymorph' => [
                'class' => PolymorphicRelation::class,
                'classAttribute' => 'source_class',
                'pkAttribute' => 'source_pk',
                'mustBeInstanceOf' => [
                    \yii\db\ActiveRecord::class,
                ],
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function init()
    {
        parent::init();
        if ($this->is_viewed === null) {
            $this->is_viewed = false;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notification}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['class', 'user_id'], 'required'],
            [['is_viewed'], 'boolean'],
            [['is_viewed'], 'default', 'value' => 0],
            [['user_id', 'sender_user_id', 'source_pk'], 'integer'],
            [['class', 'source_class'], 'string', 'max' => 100],
            [['sender_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['sender_user_id' => 'id']
            ],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * @param array $params
     * @return null|ActiveRecord
     */
    public function getBaseModel($params = [])
    {
        if (class_exists($this->class)) {
            $params['source'] = $this->getPolymorphicRelation();
            $params['sender'] = $this->sender;
            $params['record'] = $this;

            $object = new $this->class;
            Yii::configure($object, [
                'source' => $this->getPolymorphicRelation(),
                'sender' => $this->sender,
                'record' => $this,
            ]);

            return $object;
        }

        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::class, ['id' => 'sender_user_id']);
    }

    /**
     * @return ActiveRecord
     */
    public function getSourceObject()
    {
        $sourceClass = $this->source_class;
        if (class_exists($sourceClass) && !empty($sourceClass)) {
            /** @var $sourceClass ActiveRecord */
            return $sourceClass::findOne(['id' => $this->source_pk]);
        }

        return null;
    }
}
