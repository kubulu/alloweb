<?php

namespace app\models;

use app\forms\LoginForm;
use app\models\query\UserQuery;
use app\helpers\Password;
use app\components\UserMailer;
use Carbon\Carbon;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\Application as WebApplication;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $unconfirmed_email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $registration_ip
 * @property integer $confirmed_at
 * @property integer $blocked_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_at
 * @property integer $flags
 *
 * @property string $lastOnlineDatetime
 * @property Account[] $accounts
 * @property Guest[] $guests
 * @property Profile $profile
 * @property-read bool $isAdmin
 * @property-read bool $isBlocked
 * @property-read bool $isConfirmed
 * @property-read bool $isOnline
 * @property-read bool $isPremium
 * @property-read Photo[] $photos
 * @property-read UserFinder $finder
 * @property-read UserMailer $mailer
 * @property-read UserBoost $boost
 * @property-read UserPremium $premium
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const BEFORE_CREATE = 'beforeCreate';
    const AFTER_CREATE = 'afterCreate';
    const BEFORE_REGISTER = 'beforeRegister';
    const AFTER_REGISTER = 'afterRegister';
    const BEFORE_CONFIRM = 'beforeConfirm';
    const AFTER_CONFIRM = 'afterConfirm';
    const OLD_EMAIL_CONFIRMED = 0b1;
    const NEW_EMAIL_CONFIRMED = 0b10;

    /**
     * @var string
     */
    public $password;
    /**
     * @var int
     */
    public $photosCount;
    /**
     * @var Profile
     */
    private $_profile;
    /**
     * @var string
     */
    public static $usernameRegexp = '/^[-a-zA-Z0-9_\.@]+$/';

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @param $id
     * @return null|static
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @param $token
     * @param null $type
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Method "' . __CLASS__ . '::' . __METHOD__ . '" is not implemented.');
    }

    /**
     * @return UserQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return ArrayHelper::merge($scenarios, [
            'register' => ['username', 'email', 'password'],
            'connect' => ['username', 'email', 'password'],
            'create' => ['username', 'email', 'password'],
            'update' => ['username', 'email', 'password'],
            'settings' => ['username', 'email', 'password'],
        ]);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            // username rules
            'usernameTrim' => ['username', 'trim'],
            'usernameRequired' => ['username', 'required', 'on' => ['register', 'create', 'connect', 'update']],
            'usernameMatch' => ['username', 'match', 'pattern' => static::$usernameRegexp],
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernameUnique' => [
                'username',
                'unique',
                'message' => Yii::t('app', 'This username has already been taken')
            ],

            // email rules
            'emailTrim' => ['email', 'trim'],
            'emailRequired' => ['email', 'required', 'on' => ['register', 'connect', 'create', 'update']],
            'emailPattern' => ['email', 'email'],
            'emailLength' => ['email', 'string', 'max' => 255],
            'emailUnique' => [
                'email',
                'unique',
                'message' => Yii::t('app', 'This email address has already been taken')
            ],

            // password rules
            'passwordRequired' => ['password', 'required', 'on' => ['register']],
            'passwordLength' => ['password', 'string', 'min' => 6, 'max' => 72, 'on' => ['register', 'create']],
        ];
    }

    /**
     * @param $authKey
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAttribute('auth_key') === $authKey;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'registration_ip' => Yii::t('app', 'Registration ip'),
            'unconfirmed_email' => Yii::t('app', 'New email'),
            'password' => Yii::t('app', 'Password'),
            'created_at' => Yii::t('app', 'Registration time'),
            'last_login_at' => Yii::t('app', 'Last login'),
            'confirmed_at' => Yii::t('app', 'Confirmation time'),
        ];
    }

    /**
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function create()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            $this->password = $this->password == null ? Password::generate(8) : $this->password;

            $this->trigger(self::BEFORE_CREATE);

            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }

            $this->confirm();

            $this->mailer->sendWelcomeMessage($this, null, true);
            $this->trigger(self::AFTER_CREATE);

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::warning($e->getMessage());
            throw $e;
        }
    }

    /**
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function register()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            $requireEmailVerification = Yii::$app->settings->get('frontend', 'siteRequireEmailVerification', true);
            $this->confirmed_at = $requireEmailVerification ? null : time();

            $this->trigger(self::BEFORE_REGISTER);

            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }

            if ($requireEmailVerification) {
                /** @var Token $token */
                $token = Yii::createObject(['class' => Token::class, 'type' => Token::TYPE_CONFIRMATION]);
                $token->link('user', $this);
            }

            $this->mailer->sendWelcomeMessage($this, isset($token) ? $token : null);
            $this->trigger(self::AFTER_REGISTER);

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::warning($e->getMessage());
            throw $e;
        }
    }

    /**
     * @param $code
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function attemptConfirmation($code)
    {
        $token = $this->finder->findTokenByParams($this->id, $code, Token::TYPE_CONFIRMATION);

        if ($token instanceof Token && !$token->isExpired) {
            $token->delete();
            if (($success = $this->confirm())) {
                Yii::$app->user->login($this, (new LoginForm($this->finder))->rememberFor);
                $message = Yii::t('app', 'Thank you, registration is now complete.');
            } else {
                $message = Yii::t('app', 'Something went wrong and your account has not been confirmed.');
            }
        } else {
            $success = false;
            $message = Yii::t('app', 'The confirmation link is invalid or expired. Please try requesting a new one.');
        }

        Yii::$app->session->setFlash($success ? 'success' : 'danger', $message);

        return $success;
    }

    /**
     * @return bool
     */
    public function resendPassword()
    {
        $this->password = Password::generate(8);
        $this->save(false, ['password_hash']);

        return $this->mailer->sendGeneratedPassword($this, $this->password);
    }

    /**
     * @param $code
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function attemptEmailChange($code)
    {
        /** @var Token $token */
        $token = $this->finder->findToken([
            'user_id' => $this->id,
            'code' => $code,
        ])->andWhere(['in', 'type', [Token::TYPE_CONFIRM_NEW_EMAIL, Token::TYPE_CONFIRM_OLD_EMAIL]])->one();

        if (empty($this->unconfirmed_email) || $token === null || $token->isExpired) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Your confirmation token is invalid or expired'));
        } else {
            $token->delete();

            if (empty($this->unconfirmed_email)) {
                Yii::$app->session->setFlash('danger', Yii::t('app', 'An error occurred processing your request'));
            } elseif ($this->finder->findUser(['email' => $this->unconfirmed_email])->exists() == false) {
                $this->email = $this->unconfirmed_email;
                $this->unconfirmed_email = null;
                Yii::$app->session->setFlash('success', Yii::t('app', 'Your email address has been changed'));

                $this->save(false);
            }
        }
    }

    /**
     * @return bool
     */
    public function confirm()
    {
        $this->trigger(self::BEFORE_CONFIRM);
        $result = (bool)$this->updateAttributes(['confirmed_at' => time()]);
        $this->trigger(self::AFTER_CONFIRM);

        return $result;
    }

    /**
     * @param $password
     * @return bool
     * @throws \yii\base\Exception
     */
    public function resetPassword($password)
    {
        return (bool)$this->updateAttributes(['password_hash' => Password::hash($password)]);
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function block()
    {
        return (bool)$this->updateAttributes([
            'blocked_at' => time(),
            'auth_key' => Yii::$app->security->generateRandomString(),
        ]);
    }

    /**
     * @return bool
     */
    public function unblock()
    {
        return (bool)$this->updateAttributes(['blocked_at' => null]);
    }

    /**
     * @return string
     */
    public function generateUsername()
    {
        $username = explode('@', $this->email)[0];
        $this->username = $username;
        if ($this->validate(['username'])) {
            return $this->username;
        }

        if (!preg_match(self::$usernameRegexp, $username)) {
            $username = 'user';
        }
        $this->username = $username;
        $max = $this->finder->userQuery->max('id');

        do {
            $this->username = $username . ++$max;
        } while (!$this->validate(['username']));

        return $this->username;
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->setAttribute('auth_key', Yii::$app->security->generateRandomString());
            if (Yii::$app instanceof WebApplication) {
                $this->setAttribute('registration_ip', Yii::$app->request->userIP);
            }
        }

        if (!empty($this->password)) {
            $this->setAttribute('password_hash', Password::hash($this->password));
        }

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\base\InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            if ($this->_profile == null) {
                $this->_profile = Yii::createObject(Profile::class);
            }
            $this->_profile->link('user', $this);
        }
    }

    /**
     * @return UserFinder|object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function getFinder()
    {
        return Yii::$container->get(UserFinder::class);
    }

    /**
     * @return UserMailer|object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function getMailer()
    {
        return Yii::$container->get(UserMailer::class);
    }

    /**
     * @return bool
     */
    public function getIsConfirmed()
    {
        return $this->confirmed_at != null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id']);
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile)
    {
        $this->_profile = $profile;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoost()
    {
        return $this->hasOne(UserBoost::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPremium()
    {
        return $this->hasOne(UserPremium::class, ['user_id' => 'id']);
    }

    /**
     * @return bool
     */
    public function getIsPremium()
    {
        if ($this->premium == null) {
            return false;
        }

        return $this->premium->isPremium;
    }

    /**
     * @return bool
     */
    public function getIsBlocked()
    {
        return $this->blocked_at != null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::class, ['user_id' => 'id']);
    }

    /**
     * @return bool
     */
    public function getIsAdmin()
    {
        return isset($this->admin);
    }

    /**
     * @return Account[] Connected accounts ($provider => $account)
     */
    public function getAccounts()
    {
        $connected = [];
        $accounts = $this->hasMany(Account::class, ['user_id' => 'id'])->all();

        /** @var Account $account */
        foreach ($accounts as $account) {
            $connected[$account->provider] = $account;
        }

        return $connected;
    }

    /**
     * @param  string $provider
     * @return Account|null
     */
    public function getAccountByProvider($provider)
    {
        $accounts = $this->getAccounts();
        return isset($accounts[$provider])
            ? $accounts[$provider]
            : null;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * @return mixed
     */
    public function getAuthKey()
    {
        return $this->getAttribute('auth_key');
    }

    /**
     * @return bool
     */
    public function getIsOnline()
    {
        if ($this->isPremium) {
            if (!$this->premium->show_online_status) {
                return false;
            }
        }

        return isset($this->last_login_at) ? time() - $this->last_login_at <= Yii::$app->params['onlineThreshold'] : false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(Photo::class, ['user_id' => 'id']);
    }

    /**
     * @return int|string
     */
    public function getPhotosCount()
    {
        return $this->getPhotos()->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUserLikes()
    {
        return $this->hasMany(Like::class, ['from_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToUserLikes()
    {
        return $this->hasMany(Like::class, ['to_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGuests()
    {
        return $this->hasMany(Guest::class, ['visited_user_id' => 'id']);
    }

    /**
     * @return mixed
     */
    public function deletePhoto()
    {
        $this->profile->photo_id = null;

        return $this->profile->save();
    }

    /**
     * @return string
     */
    public function getLastTimeOnline()
    {
        if (!isset($this->last_login_at)) {
            return Yii::t('app', 'never');
        }

        $date = Carbon::createFromTimestamp($this->last_login_at);
        $difference = $date->diffInDays();
        if ($difference == 0 || $difference == 1) {
            return $date->format('H:i');
        } elseif ($difference > 1 && $difference <= 7) {
            return Yii::t('app', 'this week');
        } elseif ($difference > 7 && $difference <= 30) {
            return Yii::t('app', 'this month');
        }

        return Yii::t('app', 'long time ago');
    }

    /**
     * @param $time
     */
    public function updateOnline($time)
    {
        $this->last_login_at = $time;
        $this->save();
    }
}
