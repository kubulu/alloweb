<?php

namespace app\controllers;

use app\base\Controller;
use app\managers\LikeManager;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class DashboardController extends Controller
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
        ];
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        $newMembersDataProvider = new ActiveDataProvider([
            'query' => $this->userManager->getQuery()->newUsers(7),
            'pagination' => ['pageSize' => 8]
        ]);

        $mutualOnline = $this->likeManager->getUsersQuery([
            'userId' => Yii::$app->user->id,
            'type' => LikeManager::TYPE_MUTUAL,
            'order' => 'user.last_login_at desc',
            'limit' => 6,
        ])->all();

        return $this->render('index', [
            'newMembersDataProvider' => $newMembersDataProvider,
            'mutualOnline' => $mutualOnline,
            'user' => $this->getCurrentUser(),
            'profile' => $this->getCurrentUserProfile(),
        ]);
    }
}
