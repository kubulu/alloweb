<?php

namespace app\controllers;

use app\forms\MessageForm;
use app\forms\ReportForm;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class ProfileController extends \app\base\Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $hideProfiles = Yii::$app->settings->get('frontend', 'siteHideUsersFromGuests', false) &&
            Yii::$app->user->isGuest;

        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'actions' => ['index', 'view'], 'roles' => ['@']],
                    ['allow' => true, 'actions' => ['view'], 'roles' => [$hideProfiles ? '@' : '?']],
                ],
            ],
        ];
    }

    /**
     * Profile page (logged-in user)
     */
    public function actionIndex()
    {
        return $this->redirect(['profile/view', 'username' => Yii::$app->user->identity->username]);
    }

    /**
     * Profile page
     *
     * @param $username
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView($username)
    {
        $user = $this->findUser($username);
        $likeByCurrentUser = null;
        $blockByCurrentUser = false;
        if (!Yii::$app->user->isGuest) {
            $likeByCurrentUser = $this->likeManager->getUserLike($this->getCurrentUser(), $user);
            $blockByCurrentUser = $this->userManager->isUserBlocked(Yii::$app->user->id, $user->id);
            $this->guestManager->trackVisit($this->getCurrentUser(), $user);
        }

        return $this->render('view', [
            'user' => $user,
            'profile' => $user->profile,
            'newMessageForm' => new MessageForm(),
            'reportForm' => new ReportForm(),
            'likeByCurrentUser' => $likeByCurrentUser,
            'blockByCurrentUser' => $blockByCurrentUser,
            'photos' => $this->photoManager->getPhotosProvider([
                'userId' => $user->id,
                'pagination' => false,
            ])->getModels(),
        ]);
    }

    /**
     * Find user by username
     *
     * @param $username
     * @return \app\models\User|\yii\web\IdentityInterface
     * @throws NotFoundHttpException
     */
    protected function findUser($username = null)
    {
        if (!Yii::$app->user->isGuest && $username == null) {
            return Yii::$app->user->identity;
        }

        /** @var $user \app\models\User */
        $user = $this->userManager->getUser($username);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        return $user;
    }
}
