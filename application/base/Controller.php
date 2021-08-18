<?php

namespace app\base;

use app\traits\ManagersTrait;
use app\traits\CurrentUserTrait;
use Yii;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\base
 */
class Controller extends \yii\web\Controller
{
    const EVENT_BEFORE_INIT = 'beforeInit';
    const EVENT_AFTER_INIT = 'afterInit';

    use ManagersTrait;
    use CurrentUserTrait;

    /**
     * @var bool
     */
    public $prepareData = true;
    /**
     * @var ActiveRecord
     */
    public $model;

    public function init()
    {
        $this->trigger(self::EVENT_BEFORE_INIT);
        parent::init();
        $this->initManagers();
        $this->trigger(self::EVENT_AFTER_INIT);
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (!Yii::$app->user->isGuest && $this->prepareData) {
            $this->initUserData($this);
            $this->updateOnline();
        }

        return parent::beforeAction($action);
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($view, $params);
        }

        return parent::render($view, $params);
    }

    /**
     * @param $params
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function findModel($params)
    {
        if (!is_array($params)) {
            $params = ['id' => $params];
        }
        $model = call_user_func([$this->model, 'find'])->where($params)->one();
        if ($model == null) {
            throw new NotFoundHttpException('Model not found');
        }

        return $model;
    }

    /**
     * @return \app\models\User|array|null|ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function getTargetUser()
    {
        if (($userId = Yii::$app->request->get('toUserId')) !== null) {
            $user = $this->userManager->getUserById($userId);
            if ($user !== null) {
                return $user;
            }
        }

        throw new NotFoundHttpException('Target user not found');
    }

    protected function updateOnline()
    {
        $user = $this->getCurrentUser();
        $lastOnline = Yii::$app->session->get('lastOnline');
        if ($user && ($lastOnline == null || time() - $lastOnline > Yii::$app->params['onlineThreshold']) ){
            $lastOnline = time();
            Yii::$app->session->set('lastOnline', $lastOnline);
            $user->updateOnline($lastOnline);
        }
    }

    /**
     * @param $data
     * @throws \yii\base\ExitException
     */
    protected function sendJson($data)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = $data;
        Yii::$app->response->send();
        Yii::$app->end();
    }
}
