<?php

namespace app\controllers;

use app\actions\ErrorAction;
use app\actions\ViewAction;
use app\components\AppState;
use app\components\ConsoleRunner;
use Yii;
use yii\captcha\CaptchaAction;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class SiteController extends \app\base\Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'page' => [
                'class' => ViewAction::class,
            ]
        ];
    }

    /**
     * @return string
     * @throws \yii\base\ExitException
     */
    public function actionApplyUpdates()
    {
        $autoUpdate = Yii::$app->params['autoApplyUpdates'];
        $appState = new AppState();
        $appState->readState();

        if (!$appState->isMaintenance()) {
            if (Yii::$app->request->isAjax) {
                return $this->sendJson(['success' => true, 'updated' => true]);
            }
            return $this->redirect('/');
        }

        $isAdmin = !Yii::$app->user->isGuest && $this->getCurrentUser()->isAdmin;
        if ($autoUpdate || ($isAdmin && Yii::$app->request->get('runUpdate', 0) == 1)) {
            $consoleRunner = new ConsoleRunner();
            $consoleRunner->run('update/apply');
            if ($isAdmin) {
                Yii::$app->session->setFlash('updateSuccess', Yii::t('app', 'YouDate has been updated'));
                return $this->redirect('/' . env('ADMIN_PREFIX'));
            }
        }

        $this->layout = 'maintenance';
        return $this->render($isAdmin ? 'update' : 'maintenance');
    }

    /**
     * @param $country
     * @param $query
     * @throws \yii\base\ExitException
     */
    public function actionFindCities($country, $query)
    {
        $this->sendJson([
            'cities' =>  Yii::$app->geographer->findCities($country, $query),
        ]);
    }
}
