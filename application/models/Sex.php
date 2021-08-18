<?php

namespace app\models;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property int $sex
 * @property string $alias
 * @property string $title
 * @property string $title_plural
 * @property string $icon
 */
class Sex extends \yii\db\ActiveRecord
{
    const MODELS_CACHE_KEY = 'sexModels';

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%sex}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'alias'], 'required'],
            [['sex'], 'integer'],
            [['alias'], 'unique'],
            [['alias'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/u'],
            [['title', 'title_plural', 'icon'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sex' => Yii::t('app', 'Sex'),
            'alias' => Yii::t('app', 'Alias'),
            'title' => Yii::t('app', 'Title'),
            'title_plural' => Yii::t('app', 'Title (plural)'),
            'icon' => Yii::t('app', 'Icon CSS class'),
        ];
    }

    /**
     * @return query\SexQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new \app\models\query\SexQuery(get_called_class());
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $modelsCount = (int) static::find()->count();
            $this->sex =  2 ** $modelsCount;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->delete(self::MODELS_CACHE_KEY);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->cache->delete(self::MODELS_CACHE_KEY);
    }

    /**
     * @param bool $plural
     * @return string
     */
    public function getTitle($plural = false)
    {
        return Yii::t('app', $plural ? $this->title_plural : $this->title);
    }
}
