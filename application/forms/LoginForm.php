<?php

namespace app\forms;

use app\models\UserFinder;
use app\helpers\Password;
use app\traits\CaptchaRequired;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class LoginForm extends \yii\base\Model
{
    use CaptchaRequired;

    /**
     * @var string
     */
    public $login;
    /**
     * @var string
     */
    public $password;
    /**
     * @var bool
     */
    public $rememberMe = false;
    /**
     * @var int
     */
    public $rememberFor = 1209600;
    /**
     * @var string
     */
    public $captcha;
    /**
     * @var \app\models\User
     */
    protected $user;
    /**
     * @var UserFinder
     */
    protected $finder;

    /**
     * @param UserFinder $finder
     * @param array $config
     */
    public function __construct(UserFinder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'login' => Yii::t('app', 'Login'),
            'password' => Yii::t('app', 'Password'),
            'rememberMe' => Yii::t('app', 'Remember me next time'),
            'captcha' => Yii::t('app', 'Captcha'),
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            'loginTrim' => ['login', 'trim'],
            'requiredFields' => [['login', 'password'], 'required'],
            'confirmationValidate' => [
                'login',
                function ($attribute) {
                    if ($this->user !== null) {
                        if (!$this->user->getIsConfirmed()) {
                            $this->addError($attribute, Yii::t('app', 'You need to confirm your email address'));
                        }
                        if ($this->user->getIsBlocked()) {
                            $this->addError($attribute, Yii::t('app', 'Your account has been blocked'));
                        }
                    }
                }
            ],
            'rememberMe' => ['rememberMe', 'boolean'],
            'passwordValidate' => ['password', function ($attribute) {
                if ($this->user === null || !Password::validate($this->password, $this->user->password_hash)) {
                    $this->addError($attribute, Yii::t('app', 'Invalid login or password'));
                }
            }]
        ];

        if ($this->isCaptchaRequired()) {
            $rules['captcha'] = ['captcha', 'captcha'];
        }

        return $rules;
    }

    /**
     * Validates form and logs the user in.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate() && $this->user) {
            $isLogged = Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->rememberFor : 0);

            if ($isLogged) {
                $this->user->updateAttributes(['last_login_at' => time()]);
            }

            return $isLogged;
        }

        return false;
    }


    /**
     * @return string
     */
    public function formName()
    {
        return 'login-form';
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->user = $this->finder->findUserByUsernameOrEmail(trim($this->login));

            return true;
        }

        return false;
    }
}
