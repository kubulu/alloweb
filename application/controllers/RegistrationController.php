<?php

namespace app\controllers;

use app\forms\LoginForm;
use app\forms\RegistrationForm;
use app\forms\ResendForm;
use app\models\Profile;
use app\models\User;
use app\models\UserFinder;
use app\traits\AjaxValidationTrait;
use app\traits\EventTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class RegistrationController extends \app\base\Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered after creating RegistrationForm class.
     * Triggered with \app\components\user\events\FormEvent.
     */
    const EVENT_BEFORE_REGISTER = 'beforeRegister';
    /**
     * Event is triggered after successful registration.
     * Triggered with \app\components\user\events\FormEvent.
     */
    const EVENT_AFTER_REGISTER = 'afterRegister';
    /**
     * Event is triggered before connecting user to social account.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_CONNECT = 'beforeConnect';
    /**
     * Event is triggered after connecting user to social account.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_CONNECT = 'afterConnect';
    /**
     * Event is triggered before confirming user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_CONFIRM = 'beforeConfirm';
    /**
     * Event is triggered before confirming user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_CONFIRM = 'afterConfirm';
    /**
     * Event is triggered after creating ResendForm class.
     * Triggered with \app\components\user\events\FormEvent.
     */
    const EVENT_BEFORE_RESEND = 'beforeResend';
    /**
     * Event is triggered after successful resending of confirmation email.
     * Triggered with \app\components\user\events\FormEvent.
     */
    const EVENT_AFTER_RESEND = 'afterResend';
    /**
     * @var UserFinder
     */
    protected $finder;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param UserFinder $finder
     * @param array $config
     */
    public function __construct($id, $module, UserFinder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'actions' => ['register', 'connect'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['confirm', 'resend'], 'roles' => ['?', '@']],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws \Exception
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRegister()
    {
        /** @var RegistrationForm $model */
        $model = Yii::createObject(RegistrationForm::class);
        $event = $this->getFormEvent($model);

        $this->trigger(self::EVENT_BEFORE_REGISTER, $event);
        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->create()) {
            $this->trigger(self::EVENT_AFTER_REGISTER, $event);
            if (Yii::$app->settings->get('frontend', 'siteRequireEmailVerification', true) == false) {
                Yii::$app->user->login($model->getUser());
                return $this->redirect(['/settings/profile']);
            } else {
                Yii::$app->session->setFlash('info',
                    Yii::t('app', 'Your account has been created and a message with further instructions has been sent to your email')
                );
            }
            return $this->render('/message', [
                'title' => Yii::t('app', 'Your account has been created'),
            ]);
        }

        return $this->render('register', [
            'model'  => $model,
            'countries' => Yii::$app->geographer->getCountriesList(),
            'sexOptions' => (new Profile())->getSexOptions(),
        ]);
    }

    /**
     * @param string $code
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionConnect($code)
    {
        $account = $this->finder->findAccount()->byCode($code)->one();
        $loginForm = new LoginForm($this->finder);
        if ($account === null || $account->getIsConnected()) {
            throw new NotFoundHttpException();
        }

        $event = $this->getConnectEvent($account, new User());
        $this->trigger(self::EVENT_BEFORE_CONNECT, $event);

        /** @var RegistrationForm $model */
        $model = Yii::createObject(RegistrationForm::class);
        $model->setAccount($account);
        $model->email = $account->email;
        $model->username = $account->username;
        if ($model->load(Yii::$app->request->post()) && $model->create(true)) {
            $account->connect($model->getUser());
            $this->trigger(self::EVENT_AFTER_CONNECT, $event);
            Yii::$app->user->login($model->getUser(), $loginForm->rememberFor);
            return $this->goBack();
        }

        return $this->render('connect', [
            'model' => $model,
            'account' => $account,
            'countries' => Yii::$app->geographer->getCountriesList(),
            'sexOptions' => (new Profile())->getSexOptions(),
        ]);
    }

    /**
     * @param $id
     * @param $code
     * @return string
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionConfirm($id, $code)
    {
        $user = $this->finder->findUserById($id);
        $event = $this->getUserEvent($user);
        $this->trigger(self::EVENT_BEFORE_CONFIRM, $event);
        $user->attemptConfirmation($code);
        $this->trigger(self::EVENT_AFTER_CONFIRM, $event);

        return $this->render('/message', [
            'title' => Yii::t('app', 'Account confirmation'),
        ]);
    }

    /**
     * @return string
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionResend()
    {
        /** @var ResendForm $model */
        $model = Yii::createObject(ResendForm::class);
        $event = $this->getFormEvent($model);

        $this->trigger(self::EVENT_BEFORE_RESEND, $event);
        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->resend()) {
            $this->trigger(self::EVENT_AFTER_RESEND, $event);

            return $this->render('/message', [
                'title' => Yii::t('app', 'A new confirmation link has been sent'),
            ]);
        }

        return $this->render('resend', [
            'model' => $model,
        ]);
    }
}
