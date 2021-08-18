<?php

namespace app\models;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property string $id
 * @property string $language
 * @property string $translation
 * @property LanguageSource $languageSource
 * @property Language $language0
 */
class LanguageTranslate extends \yii\db\ActiveRecord
{
    /**
     * @var int Number of translated language elements.
     */
    public $cnt;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%language_translate}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'language'], 'required'],
            [['id'], 'integer'],
            [['id'], 'exist', 'targetClass' => LanguageSource::class],
            [['language'], 'exist', 'targetClass' => Language::class, 'targetAttribute' => 'language_id'],
            [['translation'], 'string'],
            [['language'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'language' => Yii::t('app', 'Language'),
            'translation' => Yii::t('app', 'Translation'),
        ];
    }

    /**
     * @return array
     */
    public function getTranslatedLanguageNames()
    {
        $translatedLanguages = $this->getTranslatedLanguages();

        $data = [];
        foreach ($translatedLanguages as $languageTranslate) {
            $data[$languageTranslate->language] = $languageTranslate->getLanguageName();
        }

        return $data;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getTranslatedLanguages()
    {
        return static::find()->where('id = :id AND language != :language', [
            ':id' => $this->id, 'language' => $this->language
        ])->all();
    }

    /**
     * @return string
     */
    public function getLanguageName()
    {
        static $language_names;
        if (!$language_names || empty($language_names[$this->language])) {
            $language_names = Language::getLanguageNames();
        }

        return empty($language_names[$this->language]) ? $this->language : $language_names[$this->language];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(LanguageSource::class, ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageSource()
    {
        return $this->hasOne(LanguageSource::class, ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage0()
    {
        return $this->hasOne(Language::class, ['language_id' => 'language']);
    }
}
