<?php

namespace app\modules\admin\actions\translations;

use app\modules\admin\controllers\LanguageController;
use app\modules\admin\components\translations\Scanner;
use app\models\LanguageSource;
use yii\data\ArrayDataProvider;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\actions\translations
 * @property LanguageController $controller
 */
class ScanAction extends \yii\base\Action
{
    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function run()
    {
        $scanner = new Scanner();
        $scanner->run();

        $newDataProvider = $this->controller->createLanguageSourceDataProvider($scanner->getNewLanguageElements());
        $oldDataProvider = $this->_createLanguageSourceDataProvider($scanner->getRemovableLanguageSourceIds());

        return $this->controller->render('scan', [
            'newDataProvider' => $newDataProvider,
            'oldDataProvider' => $oldDataProvider,
        ]);
    }

    /**
     * @param $languageSourceIds
     * @return ArrayDataProvider
     */
    private function _createLanguageSourceDataProvider($languageSourceIds)
    {
        /** @var LanguageSource[] $languageSources */
        $languageSources = LanguageSource::find()->with('languageTranslates')->where(['id' => $languageSourceIds])->all();

        $data = [];
        foreach ($languageSources as $languageSource) {
            $languages = [];
            if ($languageSource->languageTranslates) {
                foreach ($languageSource->languageTranslates as $languageTranslate) {
                    $languages[] = $languageTranslate->language;
                }
            }

            $data[] = [
                'id' => $languageSource->id,
                'category' => $languageSource->category,
                'message' => $languageSource->message,
                'languages' => implode(', ', $languages),
            ];
        }

        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false,
        ]);
    }
}
