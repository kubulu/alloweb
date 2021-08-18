<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\models\query\PhotoQuery;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 * @property integer $id
 * @property integer $user_id
 * @property integer $width
 * @property integer $height
 * @property string $source
 * @property integer $is_verified
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property float $ratio
 * @property User $user
 */
class Photo extends \yii\db\ActiveRecord
{
    const VERIFIED = 1;
    const NOT_VERIFIED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%photo}}';
    }

    /**
     * @inheritdoc
     * @return PhotoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PhotoQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ];

        if (Yii::$app instanceof yii\web\Application) {
            $behaviors['blameable'] = [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ];
        }

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source'], 'required'],
            [['is_verified', 'created_at', 'updated_at'], 'integer'],
            [['source'], 'string', 'max' => 500],
            [['user_id'], 'integer'],
            [['width', 'height'], 'number', 'integerOnly' => true, 'min' => 1, 'max' => 10000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app', 'User'),
            'source' => Yii::t('app', 'Source'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
            'is_verified' => Yii::t('app', 'Verified'),
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
     * @return null|string
     */
    public function getRatio()
    {
        if (isset($this->width) && isset($this->height) && $this->height != 0) {
            return sprintf('%.3f', $this->width / $this->height);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return Yii::$app->photoStorage->getUrl($this->source);
    }

    /**
     * @param $width
     * @param $height
     * @param string $fit
     * @return mixed
     */
    public function getThumbnail($width, $height, $fit = 'crop-center')
    {
        return Yii::$app->glide->createSignedUrl([
            'photo/thumbnail', 'id' => $this->id,
            'w' => $width, 'h' => $height, 'sharp' => 1, 'fit' => $fit,
        ], true);
    }

    /**
     * @param $width
     * @param $height
     * @param string $fit
     * @return mixed
     */
    public function getCover($width, $height, $fit = 'crop-center')
    {
        return Yii::$app->glide->createSignedUrl([
            'photo/thumbnail', 'id' => $this->id,
            'w' => $width, 'h' => $height, 'fit' => $fit,
            'blur' => 20,
        ], true);
    }
}
