<?php

namespace app\components;

use app\models\Token;
use app\models\User;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\components
 */
class UserMailer extends AppMailer
{
    /**
     * @var string
     */
    protected $welcomeSubject;
    /**
     * @var string
     */
    protected $newPasswordSubject;
    /**
     * @var string
     */
    protected $confirmationSubject;
    /**
     * @var string
     */
    protected $reconfirmationSubject;
    /**
     * @var string
     */
    protected $recoverySubject;

    /**
     * @return string
     */
    public function getWelcomeSubject()
    {
        if ($this->welcomeSubject == null) {
            $this->setWelcomeSubject(Yii::t('app', 'Welcome to {0}', $this->siteName));
        }

        return $this->welcomeSubject;
    }

    /**
     * @param string $welcomeSubject
     */
    public function setWelcomeSubject($welcomeSubject)
    {
        $this->welcomeSubject = $welcomeSubject;
    }

    /**
     * @return string
     */
    public function getNewPasswordSubject()
    {
        if ($this->newPasswordSubject == null) {
            $this->setNewPasswordSubject(Yii::t('app', 'Your password on {0} has been changed', $this->siteName));
        }

        return $this->newPasswordSubject;
    }

    /**
     * @param string $newPasswordSubject
     */
    public function setNewPasswordSubject($newPasswordSubject)
    {
        $this->newPasswordSubject = $newPasswordSubject;
    }

    /**
     * @return string
     */
    public function getConfirmationSubject()
    {
        if ($this->confirmationSubject == null) {
            $this->setConfirmationSubject(Yii::t('app', 'Confirm account on {0}', $this->siteName));
        }

        return $this->confirmationSubject;
    }

    /**
     * @param string $confirmationSubject
     */
    public function setConfirmationSubject($confirmationSubject)
    {
        $this->confirmationSubject = $confirmationSubject;
    }

    /**
     * @return string
     */
    public function getReconfirmationSubject()
    {
        if ($this->reconfirmationSubject == null) {
            $this->setReconfirmationSubject(Yii::t('app', 'Confirm email change on {0}', $this->siteName));
        }

        return $this->reconfirmationSubject;
    }

    /**
     * @param string $reconfirmationSubject
     */
    public function setReconfirmationSubject($reconfirmationSubject)
    {
        $this->reconfirmationSubject = $reconfirmationSubject;
    }

    /**
     * @return string
     */
    public function getRecoverySubject()
    {
        if ($this->recoverySubject == null) {
            $this->setRecoverySubject(Yii::t('app', 'Complete password reset on {0}', $this->siteName));
        }

        return $this->recoverySubject;
    }

    /**
     * @param string $recoverySubject
     */
    public function setRecoverySubject($recoverySubject)
    {
        $this->recoverySubject = $recoverySubject;
    }

    /**
     * @param User $user
     * @param Token $token
     * @param bool $showPassword
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendWelcomeMessage(User $user, Token $token = null, $showPassword = false)
    {
        return $this->sendMessage($user->email, $this->getWelcomeSubject(),
            'welcome', ['user' => $user, 'token' => $token, 'showPassword' => $showPassword]
        );
    }

    /**
     * @param User $user
     * @param $password
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendGeneratedPassword(User $user, $password)
    {
        return $this->sendMessage($user->email, $this->getNewPasswordSubject(),
            'new_password', ['user' => $user, 'password' => $password]
        );
    }

    /**
     * @param User $user
     * @param Token $token
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendConfirmationMessage(User $user, Token $token)
    {
        return $this->sendMessage($user->email, $this->getConfirmationSubject(),
            'confirmation', ['user' => $user, 'token' => $token]
        );
    }

    /**
     * @param User $user
     * @param Token $token
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendReconfirmationMessage(User $user, Token $token)
    {
        if ($token->type == Token::TYPE_CONFIRM_NEW_EMAIL) {
            $email = $user->unconfirmed_email;
        } else {
            $email = $user->email;
        }

        return $this->sendMessage($email, $this->getReconfirmationSubject(),
            'reconfirmation', ['user' => $user, 'token' => $token]
        );
    }

    /**
     * @param User $user
     * @param Token $token
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendRecoveryMessage(User $user, Token $token)
    {
        return $this->sendMessage($user->email, $this->getRecoverySubject(),
            'recovery', ['user' => $user, 'token' => $token]
        );
    }
}
