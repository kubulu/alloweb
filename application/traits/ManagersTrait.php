<?php

namespace app\traits;

use app\managers\BalanceManager;
use app\managers\GuestManager;
use app\managers\LikeManager;
use app\managers\MessageManager;
use app\managers\NotificationManager;
use app\managers\PhotoManager;
use app\managers\UserManager;
use app\models\User;
use Yii;
use yii\web\Controller;

/**
 * Trait ManagersTrait
 * @package app\traits
 */
trait ManagersTrait
{
    /**
     * @var UserManager
     */
    public $userManager;
    /**
     * @var PhotoManager
     */
    public $photoManager;
    /**
     * @var LikeManager
     */
    public $likeManager;
    /**
     * @var GuestManager
     */
    public $guestManager;
    /**
     * @var MessageManager
     */
    public $messageManager;
    /**
     * @var BalanceManager
     */
    public $balanceManager;
    /**
     * @var NotificationManager
     */
    public $notificationManager;

    public function initManagers()
    {
        $this->userManager = Yii::$app->userManager;
        $this->photoManager = Yii::$app->photoManager;
        $this->likeManager = Yii::$app->likeManager;
        $this->guestManager = Yii::$app->guestManager;
        $this->messageManager = Yii::$app->messageManager;
        $this->balanceManager = Yii::$app->balanceManager;
        $this->notificationManager = Yii::$app->notificationManager;
    }

    protected function initUserData(Controller $controller)
    {
        /** @var User $currentUser */
        $currentUser = $this->getCurrentUser();
        if ($currentUser->isBlocked) {
            Yii::$app->user->logout();
            Yii::$app->response->redirect(['/default/index']);
        }
        $controller->view->params['counters.messages.new'] = $this->messageManager->getNewMessagesCount($currentUser->id);
        $controller->view->params['user.id'] = $currentUser->id;
        $controller->view->params['user.displayName'] = $currentUser->profile->getDisplayName();
        $controller->view->params['user.email'] = $currentUser->email;
        $controller->view->params['user.avatar'] = $currentUser->profile->getAvatarUrl();
        $controller->view->params['user.confirmed'] = $currentUser->isConfirmed;
        $controller->view->params['user.balance'] = $this->balanceManager->getUserBalance($currentUser->id);
        $controller->view->params['user.ads.hide'] = $currentUser->isPremium;
        $controller->view->params['site.premiumFeatures.enabled'] = $this->balanceManager->isPremiumFeaturesEnabled();
        $controller->view->params['site.emoji'] = Yii::$app->emoji->getEmoji();
    }
}
