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
 * @property int $field_id
 * @property string $value
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ProfileField $field
 * @property User $user
 */
class ProfileExtra extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%profile_extra}}';
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
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'field_id'], 'required'],
            [['user_id', 'field_id', 'created_at', 'updated_at'], 'integer'],
            [['value'], 'string'],
            [['user_id', 'field_id'], 'unique', 'targetAttribute' => ['user_id', 'field_id']],
            [['field_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ProfileField::class,
                'targetAttribute' => ['field_id' => 'id']
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
            'field_id' => Yii::t('app', 'Field'),
            'value' => Yii::t('app', 'Value'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(ProfileField::class, ['id' => 'field_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @param $userId
     * @return array
     */
    public static function getExtraFields($userId)
    {
        /** @var ProfileExtra[] $extraFields */
        $extraFields = ProfileExtra::find()
            ->joinWith(['field', 'field.category'])
            ->where(['user_id' => $userId])
            ->all();

        $data = [];
        foreach ($extraFields as $extraField) {
            $data[$extraField->field->category->alias][] = $extraField;
        }

        return $data;
    }

    /**
     * @param $userId
     * @param $categoryAlias
     * @param $attribute
     * @param $value
     * @return bool
     */
    public static function saveValue($userId, $categoryAlias, $attribute, $value)
    {
        /** @var ProfileExtra $model */
        $model = static::find()
            ->joinWith(['field', 'field.category'])
            ->where([
                'user_id' => $userId,
                'profile_field_category.alias' => $categoryAlias,
                'profile_field.alias' => $attribute,
            ])->one();

        if ($model == null) {
            $model = new static();
            $profileField = ProfileField::find()
                ->joinWith(['category'])
                ->where(['profile_field.alias' => $attribute, 'profile_field_category.alias' => $categoryAlias])
                ->one();
            $model->user_id = $userId;
            $model->field_id = $profileField !== null ? $profileField->id : null;
        }

        $model->value = $value;

        return $model->save();
    }
}
