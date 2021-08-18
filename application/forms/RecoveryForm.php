<?php

namespace app\forms;

use app\components\UserMailer;
use app\models\User;
use app\models\UserFinder;
use app\models\Token;
use app\traits\CaptchaRequired;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class RecoveryForm extends \yii\base\Model
{
    use CaptchaRequired;

    const SCENARIO_REQUEST = 'request';
    const SCENARIO_RESET = 'reset';

    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $password;
    /**
     * @var string
     */
    public $captcha;
    /**
     * @var UserMailer
     */
    protected $mailer;
    /**
     * @var UserFinder
     */
    protected $finder;

    /**
     * @param UserMailer $mailer
     * @param UserFinder $finder
     * @param array $config
     */
    public function __construct(UserMailer $mailer, UserFinder $finder, $config = [])
    {
        $this->mailer = $mailer;
        $this->finder = $finder;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'captcha' => Yii::t('app', 'Captcha'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_REQUEST => ['email', 'captcha'],
            self::SCENARIO_RESET => ['password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            'emailTrim' => ['email', 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'passwordRequired' => ['password', 'required'],
            'passwordLength' => ['password', 'string', 'max' => 72, 'min' => 6],
        ];

        if ($this->isCaptchaRequired()) {
            $rules['captcha'] = ['captcha', 'captcha'];
        }

        return $rules;
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendRecoveryMessage()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->finder->findUserByEmail($this->email);

        if ($user instanceof User) {
            /** @var Token $token */
            $token = \Yii::createObject([
                'class' => Token::class,
                'user_id' => $user->id,
                'type' => Token::TYPE_RECOVERY,
            ]);

            if (!$token->save(false)) {
                return false;
            }

            if (!$this->mailer->sendRecoveryMessage($user, $token)) {
                return false;
            }
        }

        \Yii::$app->session->setFlash(
            'info',
            \Yii::t('app', 'An email has been sent with instructions for resetting your password')
        );

        return true;
    }

    /**
     * @param Token $token
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function resetPassword(Token $token)
    {
        if (!$this->validate() || $token->user === null) {
            return false;
        }

        if ($token->user->resetPassword($this->password)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Your password has been changed successfully.'));
            $token->delete();
        } else {
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('app', 'An error occurred and your password has not been changed. Please try again later.')
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'recovery-form';
    }
}
