<?php

namespace app\modules\admin\helpers;

use app\models\LanguageSource;
use app\modules\admin\components\translations\Scanner;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\helpers
 */
class Language
{
    /**
     * @param $array
     * @param array $params
     * @param null $language
     * @return array
     */
    public static function a($array, $params = [], $language = null)
    {
        $data = [];

        foreach ($array as $key => $message) {
            if (!is_array($message)) {
                $data[$key] = Yii::t(Scanner::CATEGORY_ARRAY, $message, isset($params[$key]) ? $params[$key] : [], $language);
            } else {
                $data[$key] = self::a($message, isset($params[$key]) ? $params[$key] : [], $language);
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public static function getCategories()
    {
        /** @var LanguageSource[] $languageSources */
        $languageSources = LanguageSource::find()->select('category')->distinct()->all();

        $categories = [];
        foreach ($languageSources as $languageSource) {
            $categories[$languageSource->category] = $languageSource->category;
        }

        return $categories;
    }
}
