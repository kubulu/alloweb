<?php

namespace app\modules\admin\forms;

use app\models\Language;
use app\models\LanguageSource;
use app\models\LanguageTranslate;
use yii\base\Model;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\forms
 */
class LanguageExportForm extends Model
{
    /**
     * @var string[] The languages to export
     */
    public $exportLanguages;
    /**
     * @var string The file format in which to export the data (json or xml)
     */
    public $format;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exportLanguages', 'format'], 'required'],
        ];
    }

    /**
     * @param $minimumStatus
     * @return array
     */
    public function getDefaultExportLanguages($minimumStatus)
    {
        return Language::find()
            ->select('language_id')
            ->where(['>=', 'status', $minimumStatus])
            ->column();
    }

    /**
     * @return array
     */
    public function getExportData()
    {
        $languages = Language::findAll($this->exportLanguages);
        $languageSources = LanguageSource::find()->all();
        $languageTranslations = LanguageTranslate::findAll(['language' => $this->exportLanguages]);

        $data = [
            'languages' => $languages,
            'languageSources' => $languageSources,
            'languageTranslations' => $languageTranslations,
        ];

        return $data;
    }
}
