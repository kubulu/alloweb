<?php

namespace app\models;

use Yii;
use app\modules\admin\helpers\Language as LanguageHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\models
 *
 * @property string $language_id
 * @property string $language
 * @property string $country
 * @property string $name
 * @property string $name_ascii
 * @property int $status
 * @property LanguageTranslate $languageTranslate
 * @property LanguageSource[] $languageSources
 */
class Language extends \yii\db\ActiveRecord
{
    /**
     * Status of inactive language.
     */
    const STATUS_INACTIVE = 0;
    /**
     * Status of active language.
     */
    const STATUS_ACTIVE = 1;
    /**
     * Status of ‘beta’ language.
     */
    const STATUS_BETA = 2;

    /**
     * Array containing possible states.
     *
     * @var array
     * @translate
     */
    private static $_CONDITIONS = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BETA => 'Beta',
    ];

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%language}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_id', 'language', 'country', 'name', 'name_ascii', 'status'], 'required'],
            [['language_id'], 'string', 'max' => 5],
            [['language_id'], 'unique'],
            [['language_id'], 'match', 'pattern' => '/^([a-z]{2}[_-][A-Z]{2}|[a-z]{2})$/'],
            [['language', 'country'], 'string', 'max' => 2],
            [['language', 'country'], 'match', 'pattern' => '/^[a-z]{2}$/i'],
            [['name', 'name_ascii'], 'string', 'max' => 32],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => array_keys(self::$_CONDITIONS)],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'language_id' => Yii::t('app', 'Language ID'),
            'language' => Yii::t('app', 'Language'),
            'country' => Yii::t('app', 'Country'),
            'name' => Yii::t('app', 'Name'),
            'name_ascii' => Yii::t('app', 'Name Ascii'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return Yii::t('app', self::$_CONDITIONS[$this->status]);
    }

    /**
     * @return array
     */
    public static function getStatusNames()
    {
        return LanguageHelper::a(self::$_CONDITIONS);
    }

    /**
     * @return int
     */
    public function getGridStatistic()
    {
        static $statistics;
        if (!$statistics) {
            $count = LanguageSource::find()->count();
            if ($count == 0) {
                return 0;
            }

            $languageTranslates = LanguageTranslate::find()
                ->select(['language', 'COUNT(*) AS cnt'])
                ->andWhere('translation IS NOT NULL')
                ->groupBy(['language'])
                ->all();

            foreach ($languageTranslates as $languageTranslate) {
                $statistics[$languageTranslate->language] = floor(($languageTranslate->cnt / $count) * 100);
            }
        }

        return isset($statistics[$this->language_id]) ? $statistics[$this->language_id] : 0;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageTranslate()
    {
        return $this->hasOne(LanguageTranslate::class, ['language' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     *
     * @deprecated since version 1.4.5
     */
    public function getIds()
    {
        return $this->hasMany(LanguageSource::class, ['id' => 'id'])
            ->viaTable(LanguageTranslate::tableName(), ['language' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageSources()
    {
        return $this->hasMany(LanguageSource::class, ['id' => 'id'])
            ->viaTable(LanguageTranslate::tableName(), ['language' => 'language_id']);
    }

    /**
     * @param bool $active
     * @param bool $asArray
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getLanguages($active = true, $asArray = false)
    {
        if ($active) {
            return self::find()->where(['status' => static::STATUS_ACTIVE])->asArray($asArray)->all();
        } else {
            return self::find()->asArray($asArray)->all();
        }
    }

    /**
     * @param bool $active
     * @return array
     */
    public static function getLanguageNames($active = false)
    {
        $languageNames = [];
        foreach (self::getLanguages($active, true) as $language) {
            $languageNames[$language['language_id']] = $language['name'];
        }

        return $languageNames;
    }
}
