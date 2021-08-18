<?php

namespace app\modules\admin\actions\translations;

use app\modules\admin\controllers\LanguageController;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\actions\translations
 * @property LanguageController $controller
 */
class DeleteAction extends \yii\base\Action
{
    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        $this->controller->findModel(['language_id' => $id])->delete();

        return $this->controller->redirect(['list']);
    }
}
