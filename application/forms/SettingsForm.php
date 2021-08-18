<?php

namespace app\forms;

use app\helpers\Password;
use app\components\UserMailer;
use app\models\Token;
use app\models\User;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class SettingsForm extends \yii\base\Model
{
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $newPassword;
    /**
     * @var string
     */
    public $currentPassword;
    /**
     * @var UserMailer
     */
    protected $mailer;
    /**
     * @var User
     */
    private $_user;

    /**
     * @return User|null|\yii\web\IdentityInterface
     */
    public function getUser()
    {
        if ($this->_user == null) {
            $this->_user = Yii::$app->user->identity;
        }

        return $this->_user;
    }

    /**
     * SettingsForm constructor.
     * @param UserMailer $mailer
     * @param array $config
     */
    public function __construct(UserMailer $mailer, $config = [])
    {
        $this->mailer = $mailer;
        $this->setAttributes([
            'username' => $this->user->username,
            'email' => $this->user->unconfirmed_email ?: $this->user->email,
        ], false);
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 3, 'max' => 255],
            ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/'],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            [['email', 'username'], 'unique', 'when' => function ($model, $attribute) {
                return $this->user->$attribute != $model->$attribute;
            }, 'targetClass' => User::class],
            ['newPassword', 'string', 'max' => 72, 'min' => 6],
            ['currentPassword', 'required'],
            ['currentPassword', function ($attr) {
                if (!Password::validate($this->$attr, $this->user->password_hash)) {
                    $this->addError($attr, Yii::t('app', 'Current password is not valid'));
                }
            }],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'username' => Yii::t('app', 'Username'),
            'newPassword' => Yii::t('app', 'New password'),
            'currentPassword' => Yii::t('app', 'Current password'),
        ];
    }

    /**
     * @return string
     */
    public function formName()
    {
        return 'settings-form';
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function save()
    {
        if ($this->validate()) {
            $this->user->scenario = 'settings';
            $this->user->username = $this->username;
            $this->user->password = $this->newPassword;
            if ($this->email == $this->user->email && $this->user->unconfirmed_email != null) {
                $this->user->unconfirmed_email = null;
            } elseif ($this->email != $this->user->email) {
                $this->emailChange();
            }

            return $this->user->save();
        }

        return false;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    protected function emailChange()
    {
        $this->user->unconfirmed_email = $this->email;
        /** @var Token $token */
        $token = Yii::createObject([
            'class' => Token::class,
            'user_id' => $this->user->id,
            'type' => Token::TYPE_CONFIRM_NEW_EMAIL,
        ]);
        $token->save(false);
        $this->mailer->sendReconfirmationMessage($this->user, $token);
        Yii::$app->session->setFlash(
            'info',
            Yii::t('app', 'A confirmation message has been sent to your new email address')
        );
    }
}
