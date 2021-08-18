<?php

namespace app\models;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property string $format
 */
class Currency extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%currency}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['code', 'title'], 'required'],
            [['code'], 'string', 'min' => 3, 'max' => 3],
            [['title'], 'string', 'max' => 64],
            [['format'], 'string', 'max' => 32],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => Yii::t('app', 'Code'),
            'title' => Yii::t('app', 'Title'),
            'format' => Yii::t('app', 'Format'),
        ];
    }

    /**
     * @param $code
     * @return Currency|null
     */
    public static function getCurrency($code)
    {
        if ($code == null) {
            return self::getDefaultCurrency();
        }

        $model = self::findOne(['code' => $code]);
        if ($model == null) {
            return self::getDefaultCurrency();
        }

        return $model;
    }

    /**
     * @return Currency
     */
    public static function getDefaultCurrency()
    {
        $model = new static();
        $model->code = 'USD';
        $model->title = Yii::t('app', 'American dollar');
        $model->format = '$ %s';

        return $model;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }
}
