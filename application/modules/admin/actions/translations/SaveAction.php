<?php

namespace app\modules\admin\actions\translations;

use app\modules\admin\controllers\LanguageController;
use Yii;
use yii\web\Response;
use app\modules\admin\components\translations\Generator;
use app\models\LanguageTranslate;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\actions\translations
 * @property LanguageController $controller
 */
class SaveAction extends \yii\base\Action
{
    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id', 0);
        $languageId = Yii::$app->request->post('language_id', Yii::$app->language);

        $languageTranslate = LanguageTranslate::findOne(['id' => $id, 'language' => $languageId]) ?:
            new LanguageTranslate(['id' => $id, 'language' => $languageId]);

        $languageTranslate->translation = Yii::$app->request->post('translation', '');
        if ($languageTranslate->validate() && $languageTranslate->save()) {
            $generator = new Generator($this->controller->module, $languageId);
            $generator->run();
        }

        return $languageTranslate->getErrors();
    }
}
