<?php

namespace app\traits;

use app\models\Profile;
use app\models\User;
use Yii;

/**
 * Trait CurrentUserTrait
 * @package app\utils
 */
trait CurrentUserTrait
{
    /**
     * @return null|\yii\web\IdentityInterface|User
     */
    public function getCurrentUser()
    {
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->user->identity;
        }

        return null;
    }

    /**
     * @return null|Profile
     */
    public function getCurrentUserProfile()
    {
        $user = $this->getCurrentUser();
        if ($user !== null) {
            return $user->profile;
        }

        return null;
    }
}
