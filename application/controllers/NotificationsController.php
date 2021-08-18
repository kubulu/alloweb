<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class NotificationsController extends \app\base\Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'mark-as-viewed' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param null $filters
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex($filters = null)
    {
        $filters !== null ? explode(',', $filters) : [];

        return $this->render('index', [
            'categories' => $this->notificationManager->getNotificationCategories(),
            'filters' => $filters,
            'dataProvider' => $this->notificationManager->getNotificationsProvider([
                'userId' => Yii::$app->user->id,
                'filters' => (array) $filters,
                'pageSize' => 20,
            ]),
        ]);
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionMarkAsViewed()
    {
        $this->notificationManager->markAllAsViewed(Yii::$app->user->id);

        return $this->sendJson([
            'success' => true,
            'message' => Yii::t('app', 'Notifications have been marked as viewed'),
        ]);
    }
}
