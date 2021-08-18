<?php

namespace app\models;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property string $id
 * @property string $category
 * @property string $message
 * @property string $source
 * @property string $translation
 * @property LanguageTranslate $languageTranslate0
 * @property LanguageTranslate $languageTranslate
 * @property Language[] $languages
 *
 * @property LanguageTranslate[] $languageTranslates
 */
class LanguageSource extends \yii\db\ActiveRecord
{
    const INSERT_LANGUAGE_ITEMS_LIMIT = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%language_source}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['category'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category' => Yii::t('app', 'Category'),
            'message' => Yii::t('app', 'Message'),
        ];
    }

    /**
     * @param $languageItems
     * @return int
     * @throws \yii\db\Exception
     */
    public function insertLanguageItems($languageItems)
    {
        $data = [];
        foreach ($languageItems as $category => $messages) {
            foreach (array_keys($messages) as $message) {
                $data[] = [
                    $category,
                    $message,
                ];
            }
        }

        $count = count($data);
        for ($i = 0; $i < $count; $i += self::INSERT_LANGUAGE_ITEMS_LIMIT) {
            static::getDb()
                ->createCommand()
                ->batchInsert(static::tableName(), ['category', 'message'], array_slice($data, $i, self::INSERT_LANGUAGE_ITEMS_LIMIT))
                ->execute();
        }

        return $count;
    }

    /**
     * @return string
     */
    public function getTranslation()
    {
        return $this->languageTranslate ? $this->languageTranslate->translation : '';
    }

    /**
     * @return string
     */
    public function getSource()
    {
        if ($this->languageTranslate0 && $this->languageTranslate0->translation) {
            return $this->languageTranslate0->translation;
        } else {
            return $this->message;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageTranslate0()
    {
        return $this->getLanguageTranslate();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageTranslate()
    {
        return $this->hasOne(LanguageTranslate::class, ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageTranslates()
    {
        return $this->hasMany(LanguageTranslate::class, ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(Language::class, ['language_id' => 'language'])
            ->viaTable(LanguageTranslate::tableName(), ['id' => 'id']);
    }
}
