<?php

namespace app\controllers;

use app\forms\RecoveryForm;
use app\models\Token;
use app\models\UserFinder;
use app\traits\AjaxValidationTrait;
use app\traits\EventTrait;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class RecoveryController extends \app\base\Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered before requesting password reset.
     * Triggered with \app\components\user\events\FormEvent.
     */
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    /**
     * Event is triggered after requesting password reset.
     * Triggered with \app\components\user\events\FormEvent.
     */
    const EVENT_AFTER_REQUEST = 'afterRequest';
    /**
     * Event is triggered before validating recovery token.
     * Triggered with \app\components\user\events\ResetPasswordEvent. May not have $form property set.
     */
    const EVENT_BEFORE_TOKEN_VALIDATE = 'beforeTokenValidate';
    /**
     * Event is triggered after validating recovery token.
     * Triggered with \app\components\user\events\ResetPasswordEvent. May not have $form property set.
     */
    const EVENT_AFTER_TOKEN_VALIDATE = 'afterTokenValidate';
    /**
     * Event is triggered before resetting password.
     * Triggered with \app\components\user\events\ResetPasswordEvent.
     */
    const EVENT_BEFORE_RESET = 'beforeReset';
    /**
     * Event is triggered after resetting password.
     * Triggered with \app\components\user\events\ResetPasswordEvent.
     */
    const EVENT_AFTER_RESET = 'afterReset';

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
                    ['allow' => true, 'actions' => ['request', 'reset'], 'roles' => ['?']],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRequest()
    {
        /** @var RecoveryForm $model */
        $model = Yii::createObject([
            'class' => RecoveryForm::class,
            'scenario' => RecoveryForm::SCENARIO_REQUEST,
        ]);

        $event = $this->getFormEvent($model);
        $this->performAjaxValidation($model);
        $this->trigger(self::EVENT_BEFORE_REQUEST, $event);

        if ($model->load(Yii::$app->request->post()) && $model->sendRecoveryMessage()) {
            $this->trigger(self::EVENT_AFTER_REQUEST, $event);
            return $this->render('/message', [
                'title' => Yii::t('app', 'Recovery message sent'),
            ]);
        }

        return $this->render('request', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @param $code
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionReset($id, $code)
    {
        /** @var Token $token */
        $token = $this->finder->findToken(['user_id' => $id, 'code' => $code, 'type' => Token::TYPE_RECOVERY])->one();
        if (empty($token) || !$token instanceof Token) {
            throw new NotFoundHttpException();
        }

        $event = $this->getResetPasswordEvent($token);
        $this->trigger(self::EVENT_BEFORE_TOKEN_VALIDATE, $event);

        if ($token === null || $token->isExpired || $token->user === null) {
            $this->trigger(self::EVENT_AFTER_TOKEN_VALIDATE, $event);
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('app', 'Recovery link is invalid or expired. Please try requesting a new one.')
            );
            return $this->render('/message', [
                'title' => Yii::t('app', 'Invalid or expired link'),
            ]);
        }

        /** @var RecoveryForm $model */
        $model = Yii::createObject([
            'class' => RecoveryForm::class,
            'scenario' => RecoveryForm::SCENARIO_RESET,
        ]);
        $event->setForm($model);

        $this->performAjaxValidation($model);
        $this->trigger(self::EVENT_BEFORE_RESET, $event);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->resetPassword($token)) {
            $this->trigger(self::EVENT_AFTER_RESET, $event);
            return $this->render('/message', [
                'title' => Yii::t('app', 'Password has been changed'),
            ]);
        }

        return $this->render('reset', [
            'model' => $model,
        ]);
    }
}
