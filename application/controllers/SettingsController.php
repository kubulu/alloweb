<?php

namespace app\controllers;

use app\forms\ProfileExtraForm;
use app\forms\VerificationForm;
use app\models\ProfileExtra;
use app\models\ProfileField;
use app\models\ProfileFieldCategory;
use app\models\Verification;
use app\settings\Settings;
use app\models\UserFinder;
use app\traits\AjaxValidationTrait;
use app\traits\EventTrait;
use app\forms\SettingsForm;
use app\forms\UploadForm;
use app\models\Profile;
use app\models\User;
use MenaraSolutions\Geographer\City;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class SettingsController extends \app\base\Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered before updating user's profile.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_PROFILE_UPDATE = 'beforeProfileUpdate';
    /**
     * Event is triggered after updating user's profile.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_PROFILE_UPDATE = 'afterProfileUpdate';
    /**
     * Event is triggered before updating user's account settings.
     * Triggered with \app\components\user\events\FormEvent.
     */
    const EVENT_BEFORE_ACCOUNT_UPDATE = 'beforeAccountUpdate';
    /**
     * Event is triggered after updating user's account settings.
     * Triggered with \app\components\user\events\FormEvent.
     */
    const EVENT_AFTER_ACCOUNT_UPDATE = 'afterAccountUpdate';
    /**
     * Event is triggered before changing users' email address.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_CONFIRM = 'beforeConfirm';
    /**
     * Event is triggered after changing users' email address.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_CONFIRM = 'afterConfirm';
    /**
     * Event is triggered before disconnecting social account from user.
     * Triggered with \app\components\user\events\ConnectEvent.
     */
    const EVENT_BEFORE_DISCONNECT = 'beforeDisconnect';
    /**
     * Event is triggered after disconnecting social account from user.
     * Triggered with \app\components\user\events\ConnectEvent.
     */
    const EVENT_AFTER_DISCONNECT = 'afterDisconnect';
    /**
     * Event is triggered before deleting user's account.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    /**
     * Event is triggered after deleting user's account.
     * Triggered with \app\components\user\events\UserEvent.
     */
    const EVENT_AFTER_DELETE = 'afterDelete';
    /**
     * @var UserFinder
     */
    protected $finder;

    /**
     * @var string
     */
    public $layout = '@app/views/settings/_layout';

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

    public function init()
    {
        parent::init();
        $this->initManagers();
        if (!Yii::$app->user->isGuest) {
            $this->initUserData($this);
        }
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
                    'disconnect' => ['post'],
                    'delete' => ['post', 'get'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['confirm'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionProfile()
    {
        /** @var Profile $model */
        $model = $this->finder->findProfileById(Yii::$app->user->identity->getId());

        if ($model == null) {
            $model = Yii::createObject(Profile::class);
            $model->link('user', Yii::$app->user->identity);
        }

        $event = $this->getProfileEvent($model);

        $this->performAjaxValidation($model);

        $formData = (Yii::$app->request->post('Profile'));
        if (isset($formData['looking_for_sex_array']) && is_array($formData['looking_for_sex_array'])) {
            $finalValue = 0;
            foreach ($formData['looking_for_sex_array'] as $value) {
                if ($model->isValidSexOption($value)) {
                    $finalValue += $value;
                }
            }
            $model->looking_for_sex = $finalValue;
        }

        try {
            $city = City::build($model->city);
            $model->latitude = $city->getLatitude();
            $model->longitude = $city->getLongitude();
        } catch (\Exception $e) {

        }

        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Your profile has been updated'));
            $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
            return $this->refresh();
        }

        $profileFields = ProfileField::getFields();
        $profileExtra = ProfileExtra::getExtraFields(Yii::$app->user->id);
        $profileFieldCategories = ProfileFieldCategory::find()
            ->joinWith('profileFields', 'profile')
            ->visible()->sorted()->all();

        $extraModels = [];
        foreach ($profileFieldCategories as $category) {
            $extraModels[$category->alias] = ProfileExtraForm::createFromFields(
                ArrayHelper::getValue($profileFields, $category->alias, []),
                ArrayHelper::getValue($profileExtra, $category->alias, []),
                $category->alias
            );
        }

        return $this->render('profile', [
            'model' => $model,
            'countries' => Yii::$app->geographer->getCountriesList(),
            'profileFields' => $profileFields,
            'profileFieldCategories' => $profileFieldCategories,
            'profileExtra' => $profileExtra,
            'extraModels' => $extraModels,
        ]);
    }

    /**
     * @return \yii\web\Response
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionExtraFields()
    {
        $categoryAlias = Yii::$app->request->post('categoryAlias');
        $profileFields = ProfileField::getFields($categoryAlias);
        $profileExtra = ProfileExtra::getExtraFields(Yii::$app->user->id);
        $model = ProfileExtraForm::createFromFields(
            ArrayHelper::getValue($profileFields, $categoryAlias, []),
            ArrayHelper::getValue($profileExtra, $categoryAlias, []),
            $categoryAlias
        );

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            foreach ($model->getAttributes() as $attribute => $value) {
                ProfileExtra::saveValue(Yii::$app->user->id, $categoryAlias, $attribute, $value);
            }
        }

        Yii::$app->session->setFlash('success_' . $categoryAlias,
            Yii::t('app', 'Your profile has been updated')
        );

        return $this->redirect(['profile', '#' => $categoryAlias]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAccount()
    {
        /** @var SettingsForm $model */
        $model = Yii::createObject(SettingsForm::class);
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_ACCOUNT_UPDATE, $event);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Your account details have been updated'));
            $this->trigger(self::EVENT_AFTER_ACCOUNT_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('account', [
            'model' => $model,
        ]);
    }

    /**
     * @return string
     */
    public function actionNetworks()
    {
        return $this->render('networks', [
            'user' => Yii::$app->user->identity,
        ]);
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function actionVerification()
    {
        $verificationForm = new VerificationForm();
        $verificationForm->userId = $this->getCurrentUser()->id;
        $verificationEntry = Verification::findOne(['user_id' => $this->getCurrentUser()->id]);

        if (Yii::$app->request->isPost) {
            $verificationForm->photo = UploadedFile::getInstanceByName('photo');
            if ($verificationForm->createVerificationEntry()) {
                if (Yii::$app->request->isAjax) {
                    return $this->sendJson(['success' => true]);
                }
                return $this->refresh();
            }
        }

        return $this->render('verification', [
            'verificationForm' => $verificationForm,
            'verificationEntry' => $verificationEntry,
            'user' => $this->getCurrentUser(),
            'profile' => $this->getCurrentUserProfile(),
        ]);
    }

    /**
     * @param $id
     * @param $code
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function actionConfirm($id, $code)
    {
        $user = $this->finder->findUserById($id);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        $event = $this->getUserEvent($user);

        $this->trigger(self::EVENT_BEFORE_CONFIRM, $event);
        $user->attemptEmailChange($code);
        $this->trigger(self::EVENT_AFTER_CONFIRM, $event);

        return $this->redirect(['account']);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDisconnect($id)
    {
        $account = $this->finder->findAccount()->byId($id)->one();

        if ($account === null) {
            throw new NotFoundHttpException();
        }
        if ($account->user_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        $event = $this->getConnectEvent($account, $account->user);

        $this->trigger(self::EVENT_BEFORE_DISCONNECT, $event);
        $account->delete();
        $this->trigger(self::EVENT_AFTER_DISCONNECT, $event);

        return $this->redirect(['networks']);
    }

    /**
     * @return string
     */
    public function actionPhotos()
    {
        $dataProvider = $this->photoManager->getPhotosProvider([
            'userId' => Yii::$app->user->id,
            'verifiedOnly' => false,
        ]);

        return $this->render('photos', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionUpload()
    {
        /** @var $settings Settings */
        $settings = Yii::$app->settings;

        $uploadForm = new UploadForm();
        $profile = $this->getCurrentUserProfile();
        $autoSetPhoto = !$settings->get('common', 'photoModerationEnabled', true);

        if ($uploadForm->load(Yii::$app->request->post()) && $uploadForm->validate()) {
            $photoIDs = $uploadForm->createPhotos();
            if ($autoSetPhoto && $profile->photo_id == null && count($photoIDs)) {
                $this->photoManager->resetUserPhoto($profile->user_id, $photoIDs[0]);
            }
            Yii::$app->session->set('uploadedPhotos', $photoIDs);
            return $this->redirect('photos');
        }

        return $this->render('upload', [
            'uploadForm' => new UploadForm(),
            'settings' => $settings->get('common'),
        ]);
    }

    /**
     * @return string
     */
    public function actionBlockedUsers()
    {
        return $this->render('blocked-users', [
            'blockedUsers' => $this->userManager->getBlockedUsers(Yii::$app->user->id)
        ]);
    }

    /**
     * @return \yii\web\Response|string
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete()
    {
        if (Yii::$app->request->isGet) {
            return $this->render('delete');
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;
        $event = $this->getUserEvent($user);

        Yii::$app->user->logout();

        $this->trigger(self::EVENT_BEFORE_DELETE, $event);
        $user->delete();
        $this->trigger(self::EVENT_AFTER_DELETE, $event);

        Yii::$app->session->setFlash('info', Yii::t('app', 'Your account has been completely deleted'));

        return $this->goHome();
    }
}
