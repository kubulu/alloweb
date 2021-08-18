<?php

namespace app\traits;

use app\events\AuthEvent;
use app\events\ConnectEvent;
use app\events\FormEvent;
use app\events\FromToUserEvent;
use app\events\ProfileEvent;
use app\events\ResetPasswordEvent;
use app\events\UserEvent;
use app\models\Account;
use app\models\Profile;
use app\models\Token;
use app\models\User;
use app\forms\RecoveryForm;
use Yii;
use yii\authclient\ClientInterface;
use yii\base\Model;

/**
 * @package app\traits
 */
trait EventTrait
{
    /**
     * @param  Model $form
     * @return FormEvent|object
     * @throws \yii\base\InvalidConfigException
     */
    protected function getFormEvent(Model $form)
    {
        return Yii::createObject(['class' => FormEvent::class, 'form' => $form]);
    }

    /**
     * @param  User $user
     * @return UserEvent|object
     * @throws \yii\base\InvalidConfigException
     */
    protected function getUserEvent(User $user)
    {
        return Yii::createObject(['class' => UserEvent::class, 'user' => $user]);
    }

    /**
     * @param  Profile $profile
     * @return ProfileEvent|object
     * @throws \yii\base\InvalidConfigException
     */
    protected function getProfileEvent(Profile $profile)
    {
        return Yii::createObject(['class' => ProfileEvent::class, 'profile' => $profile]);
    }

    /**
     * @param Account $account
     * @param User $user
     * @return ConnectEvent|object
     * @throws \yii\base\InvalidConfigException
     */
    protected function getConnectEvent(Account $account, User $user)
    {
        return Yii::createObject(['class' => ConnectEvent::class, 'account' => $account, 'user' => $user]);
    }

    /**
     * @param Account $account
     * @param ClientInterface $client
     * @return AuthEvent|object
     * @throws \yii\base\InvalidConfigException
     */
    protected function getAuthEvent(Account $account, ClientInterface $client)
    {
        return Yii::createObject(['class' => AuthEvent::class, 'account' => $account, 'client' => $client]);
    }

    /**
     * @param  Token $token
     * @param  RecoveryForm $form
     * @return ResetPasswordEvent|object
     * @throws \yii\base\InvalidConfigException
     */
    protected function getResetPasswordEvent(Token $token = null, RecoveryForm $form = null)
    {
        return Yii::createObject(['class' => ResetPasswordEvent::class, 'token' => $token, 'form' => $form]);
    }

    /**
     * @param User $fromUser
     * @param User $toUser
     * @param object|null $relatedData
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    protected function getFromToUserEvent(User $fromUser, User $toUser, $relatedData = null)
    {
        return Yii::createObject([
            'class' => FromToUserEvent::class,
            'fromUser' => $fromUser,
            'toUser' => $toUser,
            'relatedData' => $relatedData,
        ]);
    }
}
