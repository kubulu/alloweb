<?php

namespace app\forms;

use app\models\Account;
use app\models\Profile;
use app\models\User;
use app\traits\CaptchaRequired;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class RegistrationForm extends \yii\base\Model
{
    use CaptchaRequired;

    /**
     * @var string User email address
     */
    public $email;
    /**
     * @var string Username
     */
    public $username;
    /**
     * @var string Password
     */
    public $password;
    /**
     * @var string
     */
    public $name;
    /**
     * @var
     */
    public $sex;
    /**
     * @var string
     */
    public $country;
    /**
     * @var int
     */
    public $city;
    /**
     * @var string
     */
    public $dob;
    /**
     * @var string
     */
    public $captcha;
    /**
     * @var User
     */
    protected $user = null;
    /**
     * @var Account
     */
    protected $account = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        /** @var User $user */
        $user = Yii::createObject(User::class);
        $sexOptions = (new Profile())->getSexOptions();

        $rules = [
            // username rules
            'usernameTrim' => ['username', 'trim'],
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernamePattern' => ['username', 'match', 'pattern' => $user::$usernameRegexp],
            'usernameRequired' => ['username', 'required'],
            'usernameUnique' => [
                'username',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('app', 'This username has already been taken')
            ],
            // email rules
            'emailTrim' => ['email', 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'emailUnique' => [
                'email',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('app', 'This email address has already been taken')
            ],
            // password rules
            'passwordRequired' => ['password', 'required',],
            'passwordLength' => ['password', 'string', 'min' => 6, 'max' => 72],

            // profile rules
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'trim'],
            [['sex'], 'in', 'range' => array_keys($sexOptions)],

            // birth date
            ['dob', 'date', 'format' => 'php:Y-m-d'],
            ['dob', 'validateBirthday'],

            // country
            ['country', 'string', 'min' => 2, 'max' => 2],
            ['country', function ($attribute, $params) {
                return Yii::$app->geographer->isValidCountryCode($this->$attribute);
            }],

            // city
            ['city', 'integer', 'min' => 0],
            ['city', function ($attribute, $params) {
                return Yii::$app->geographer->isValidCityCode($this->$attribute);
            }],
        ];

        if ($this->isCaptchaRequired()) {
            $rules['captcha'] = ['captcha', 'captcha'];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'name' => Yii::t('app', 'Full name'),
            'sex' => Yii::t('app', 'Sex'),
            'country' => Yii::t('app', 'Country'),
            'city' => Yii::t('app', 'City'),
            'dob' => Yii::t('app', 'Birthdate'),
            'captcha' => Yii::t('app', 'Captcha'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'register-form';
    }

    /**
     * @param bool $connect
     * @return bool
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function create($connect = false)
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var User $user */
        $user = Yii::createObject(User::class);
        $user->setScenario($connect ? 'connect' : 'register');
        $user->setAttributes($this->attributes);

        if (!$user->register()) {
            return false;
        }

        $user->profile->sex = $this->sex;
        $user->profile->name = $this->name;
        $user->profile->country = $this->country;
        $user->profile->city = $this->city;
        $user->profile->dob = $this->dob;
        $user->profile->save();

        $this->user = $user;

        return true;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateBirthday($attribute, $params)
    {
        $date = new \DateTime();
        date_sub($date, date_interval_create_from_date_string('18 years'));
        $minAgeDate = date_format($date, 'Y-m-d');
        date_sub($date, date_interval_create_from_date_string('100 years'));
        $maxAgeDate = date_format($date, 'Y-m-d');
        if ($this->$attribute > $minAgeDate) {
            $this->addError($attribute, Yii::t('app', 'Date is too small.'));
        } elseif ($this->$attribute < $maxAgeDate) {
            $this->addError($attribute, Yii::t('app','Date is to big.'));
        }
    }
}
