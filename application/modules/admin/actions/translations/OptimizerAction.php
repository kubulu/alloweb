<?php

namespace app\modules\admin\actions\translations;

use app\modules\admin\components\translations\Optimizer;
use app\modules\admin\controllers\LanguageController;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\actions\translations
 * @property LanguageController $controller
 */
class OptimizerAction extends \yii\base\Action
{
    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $optimizer = new Optimizer();
        $optimizer->run();

        $removedLanguageElements = $optimizer->getRemovedLanguageElements();

        return $this->controller->render('optimizer', [
            'newDataProvider' => $this->controller->createLanguageSourceDataProvider($removedLanguageElements),
        ]);
    }
}
