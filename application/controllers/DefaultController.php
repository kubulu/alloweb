<?php

namespace app\controllers;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class DefaultController extends \app\base\Controller
{
    /**
     * @return int|mixed|string|\yii\console\Response
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->runAction('dashboard/index');
        }

        return $this->render('index');
    }
}
