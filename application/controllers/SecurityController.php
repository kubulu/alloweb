<?php

namespace app\controllers;


use app\traits\AjaxValidationTrait;
use app\traits\EventTrait;
use app\forms\LoginForm;
use app\helpers\Url;
use app\models\Account;
use app\models\User;
use app\models\UserFinder;
use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class SecurityController extends \app\base\Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered before logging user in.
     * Triggered with \app\components\user\events\FormEvent.
     */
    const EVENT_BEFORE_LOGIN = 'beforeLogin';
    /**
     * Event is triggered after logging user in.
     * Triggered with \app\components\user\events\FormEvent.
     */
    const EVENT_AFTER_LOGIN = 'afterLogin';
    /**
     * Event is triggered before logging user out.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_LOGOUT = 'beforeLogout';
    /**
     * Event is triggered after logging user out.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_LOGOUT = 'afterLogout';
    /**
     * Event is triggered before authenticating user via social network.
     * Triggered with \app\components\user\events\AuthEvent.
     */
    const EVENT_BEFORE_AUTHENTICATE = 'beforeAuthenticate';
    /**
     * Event is triggered after authenticating user via social network.
     * Triggered with \app\components\user\events\AuthEvent.
     */
    const EVENT_AFTER_AUTHENTICATE = 'afterAuthenticate';
    /**
     * Event is triggered before connecting social network account to user.
     * Triggered with \app\components\user\events\AuthEvent.
     */
    const EVENT_BEFORE_CONNECT = 'beforeConnect';
    /**
     * Event is triggered before connecting social network account to user.
     * Triggered with \app\components\user\events\AuthEvent.
     */
    const EVENT_AFTER_CONNECT = 'afterConnect';
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
                    ['allow' => true, 'actions' => ['login', 'auth'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['login', 'auth', 'logout'], 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::class,
                'successCallback' => Yii::$app->user->isGuest
                    ? [$this, 'authenticate']
                    : [$this, 'connect'],
            ],
        ];
    }

    /**
     * @return string|Response
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }

        /** @var LoginForm $model */
        $model = \Yii::createObject(LoginForm::class);
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->login()) {
            $this->trigger(self::EVENT_AFTER_LOGIN, $event);
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * @return Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogout()
    {
        $event = $this->getUserEvent(Yii::$app->user->identity);
        $this->trigger(self::EVENT_BEFORE_LOGOUT, $event);
        Yii::$app->getUser()->logout();
        $this->trigger(self::EVENT_AFTER_LOGOUT, $event);

        return $this->goHome();
    }

    /**
     * @param ClientInterface $client
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function authenticate(ClientInterface $client)
    {
        $account = $this->finder->findAccount()->byClient($client)->one();

        if ($account === null) {
            /** @var Account $account */
            $accountObj = \Yii::createObject(Account::class);
            $account = $accountObj::create($client);
        }

        $event = $this->getAuthEvent($account, $client);
        $this->trigger(self::EVENT_BEFORE_AUTHENTICATE, $event);

        if ($account->user instanceof User) {
            if ($account->user->isBlocked) {
                Yii::$app->session->setFlash('danger', Yii::t('app', 'Your account has been blocked.'));
                $this->action->successUrl = Url::to(['/security/login']);
            } else {
                $account->user->updateAttributes(['last_login_at' => time()]);
                Yii::$app->user->login($account->user, (new LoginForm($this->finder))->rememberFor);
                $this->action->successUrl = \Yii::$app->getUser()->getReturnUrl();
            }
        } else {
            $this->action->successUrl = $account->getConnectUrl();
        }

        $this->trigger(self::EVENT_AFTER_AUTHENTICATE, $event);
    }

    /**
     * @param ClientInterface $client
     * @throws \yii\base\InvalidConfigException
     */
    public function connect(ClientInterface $client)
    {
        /** @var Account $account */
        $account = \Yii::createObject(Account::class);
        $event = $this->getAuthEvent($account, $client);

        $this->trigger(self::EVENT_BEFORE_CONNECT, $event);

        $account->connectWithUser($client);

        $this->trigger(self::EVENT_AFTER_CONNECT, $event);

        $this->action->successUrl = Url::to(['/settings/networks']);
    }
}
