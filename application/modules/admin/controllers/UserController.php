<?php

namespace app\modules\admin\controllers;

use app\models\UserFinder;
use app\modules\admin\forms\BalanceUpdateForm;
use app\payments\AdminBonusTransaction;
use app\traits\AjaxValidationTrait;
use app\traits\EventTrait;
use app\helpers\Url;
use app\models\Admin;
use app\models\Profile;
use app\models\User;
use app\modules\admin\models\UserSearch;
use Yii;
use yii\base\ExitException;
use yii\base\Module;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\controllers
 */
class UserController extends \app\modules\admin\components\Controller
{
    use EventTrait;
    use AjaxValidationTrait;

    /**
     * @var string
     */
    public $model = User::class;

    /**
     * Event is triggered before updating existing user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_UPDATE = 'beforeUpdate';
    /**
     * Event is triggered after updating existing user.
     * Triggered with app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_UPDATE = 'afterUpdate';
    /**
     * Event is triggered before updating existing user's profile.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_PROFILE_UPDATE = 'beforeProfileUpdate';
    /**
     * Event is triggered after updating existing user's profile.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_PROFILE_UPDATE = 'afterProfileUpdate';
    /**
     * Event is triggered before confirming existing user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_CONFIRM = 'beforeConfirm';
    /**
     * Event is triggered after confirming existing user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_CONFIRM = 'afterConfirm';
    /**
     * Event is triggered before deleting existing user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    /**
     * Event is triggered after deleting existing user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_DELETE = 'afterDelete';
    /**
     * Event is triggered before blocking existing user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_BLOCK = 'beforeBlock';
    /**
     * Event is triggered after blocking existing user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_BLOCK = 'afterBlock';
    /**
     * Event is triggered before unblocking existing user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_UNBLOCK = 'beforeUnblock';
    /**
     * Event is triggered after unblocking existing user.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_UNBLOCK = 'afterUnblock';

    /**
     * @var UserFinder
     */
    protected $finder;

    /**
     * @param string $id
     * @param Module $module
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'confirm' => ['post'],
                    'resend-password' => ['post'],
                    'block' => ['post'],
                    'toggle-admin' => ['post'],
                    'toggle-verification' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        Url::remember('', 'actions-redirect');
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws ExitException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $user->scenario = 'update';
        $event = $this->getUserEvent($user);

        $this->performAjaxValidation($user);

        $this->trigger(self::EVENT_BEFORE_UPDATE, $event);
        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Account details have been updated'));
            $this->trigger(self::EVENT_AFTER_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('_account', [
            'user' => $user,
        ]);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws ExitException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdateProfile($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $profile = $user->profile;

        if ($profile == null) {
            $profile = Yii::createObject(Profile::class);
            $profile->link('user', $user);
        }
        $event = $this->getProfileEvent($profile);
        $this->performAjaxValidation($profile);
        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);

        if ($profile->load(Yii::$app->request->post()) && $profile->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Profile details have been updated'));
            $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('_profile', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws ExitException
     */
    public function actionUpdateBalance($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $profile = $user->profile;

        $balanceForm = new BalanceUpdateForm();
        $this->performAjaxValidation($balanceForm);

        if ($balanceForm->load(Yii::$app->request->post()) && $balanceForm->validate()) {
            $this->balanceManager->increase(['user_id' => $user->id], $balanceForm->amount, [
                'class' => AdminBonusTransaction::class,
                'notes' => $balanceForm->notes,
            ]);
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Balance updated'));
            return $this->refresh();
        }

        return $this->render('_balance', [
            'user' => $user,
            'profile' => $profile,
            'currentBalance' => $this->balanceManager->getUserBalance($user->id),
            'balanceForm' => $balanceForm,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInfo($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);

        return $this->render('_info', [
            'user' => $user,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        $event = $this->getUserEvent($model);

        $this->trigger(self::EVENT_BEFORE_CONFIRM, $event);
        $model->confirm();
        $this->trigger(self::EVENT_AFTER_CONFIRM, $event);

        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'User has been confirmed'));

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if ($id == Yii::$app->user->getId()) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'You can not remove your own account'));
        } else {
            $model = $this->findModel($id);
            $event = $this->getUserEvent($model);
            $this->trigger(self::EVENT_BEFORE_DELETE, $event);
            $model->delete();
            $this->trigger(self::EVENT_AFTER_DELETE, $event);
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'User has been deleted'));
        }

        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionBlock($id)
    {
        if ($id == Yii::$app->user->getId()) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'You can not block your own account'));
        } else {
            $user = $this->findModel($id);
            $event = $this->getUserEvent($user);
            if ($user->getIsBlocked()) {
                $this->trigger(self::EVENT_BEFORE_UNBLOCK, $event);
                $user->unblock();
                $this->trigger(self::EVENT_AFTER_UNBLOCK, $event);
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'User has been unblocked'));
            } else {
                $this->trigger(self::EVENT_BEFORE_BLOCK, $event);
                $user->block();
                $user->deletePhoto();
                Yii::$app->notificationManager->deleteNotificationsFrom($user);
                $this->trigger(self::EVENT_AFTER_BLOCK, $event);
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'User has been blocked'));
            }
        }

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionToggleAdmin($id)
    {
        if ($id == Yii::$app->user->getId()) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'You can not toggle your own admin status.'));
        } else {
            $user = $this->findModel($id);
            if ($user->isAdmin) {
                Admin::remove($user);
                Yii::$app->session->setFlash('success', Yii::t('app', 'User has been removed from administrators.'));
            } else {
                Admin::add($user);
                Yii::$app->session->setFlash('success', Yii::t('app', 'User has been added to administrators.'));
            }
        }

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionToggleVerification($id)
    {
        $user = $this->findModel($id);
        if ($user->profile->is_verified) {
            $user->profile->is_verified = false;
            $user->profile->save(false);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Verification badge has been removed from this user.'));
        } else {
            $user->profile->is_verified = true;
            $user->profile->save(false);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Verification badge has been added to this user.'));
        }

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * @param $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionResendPassword($id)
    {
        $user = $this->findModel($id);
        if ($user->isAdmin) {
            throw new ForbiddenHttpException(Yii::t('app', 'Password generation is not possible for admin users'));
        }

        if ($user->resendPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'New Password has been generated and sent to user'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Error while trying to generate new password'));
        }

        return $this->redirect(Url::previous('actions-redirect'));
    }
}
