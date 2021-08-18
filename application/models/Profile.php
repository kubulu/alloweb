<?php

namespace app\models;

use app\events\ProfileEvent;
use app\models\query\ProfileQuery;
use MenaraSolutions\Geographer\City;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 * @property $user_id
 * @property $photo_id
 * @property $sex integer
 * @property $dob date
 * @property $name string
 * @property $description string
 * @property $looking_for_sex integer
 * @property $looking_for_from_age integer
 * @property $looking_for_to_age integer
 * @property $status integer
 * @property $timezone string
 * @property $country string
 * @property $city integer
 * @property $latitude decimal
 * @property $longitude decimal
 * @property $is_verified bool
 * @property $language_id string
 *
 * @property $user User
 * @property $photo Photo
 * @property $sexModel Sex
 * @property $extraFields[] ProfileExtra
 */
class Profile extends \yii\db\ActiveRecord
{
    const SEX_NOT_SET = 0;
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;
    const SEX_CACHE_KEY = 'sexModels';

    const STATUS_NOT_SET = 0;
    const STATUS_SINGLE = 1;
    const STATUS_SEEING_SOMEONE = 2;
    const STATUS_MARRIED = 3;
    const STATUS_OPEN_RELATIONSHIP = 4;

    const AVATAR_SMALL = 48;
    const AVATAR_NORMAL = 200;

    const EVENT_AVATAR_FALLBACK = 'avatarFallback';

    /**
     * @var null|int
     */
    protected $_age = null;
    /**
     * @var null|string
     */
    protected $_displayLocation;
    /**
     * @var array
     */
    protected $_profileFieldCategories;
    /**
     * @var ProfileField[]
     */
    protected $_profileFields;
    /**
     * @var ProfileExtra[]
     */
    protected $_profileExtra;
    /**
     * @var array
     */
    public $looking_for_sex_array = [];
    /**
     * @var
     */
    public $fallbackAvatar;
    /**
     * @var Sex[]|bool
     */
    protected static $sexModels;

    protected static $sexModelsCounter = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }

    /**
     * @return ProfileQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new ProfileQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['description', 'string'],
            ['timezone', 'default', 'value' => 'UTC'],
            ['timezone', 'validateTimeZone'],
            ['name', 'string', 'max' => 42],
            ['sex', 'in', 'range' => array_keys($this->getSexOptions())],
            ['looking_for_sex', 'integer', 'min' => 0, 'max' => 1024*1024],
            ['looking_for_from_age', 'integer', 'min' => 18, 'max' => 100],
            ['looking_for_to_age', 'integer', 'min' => 18, 'max' => 100],
            ['status', 'in', 'range' => array_keys($this->getStatusOptions())],
            ['looking_for_from_age', 'compare', 'compareAttribute' => 'looking_for_to_age', 'operator' => '<='],

            // birthdate
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

            [['looking_for_sex_array'], 'safe'],
            [['latitude', 'longitude'], 'safe'],

            // language
            [['language_id'], 'string', 'max' => 5],
            [['language_id'], 'exist', 'targetClass' => Language::class, 'targetAttribute' => 'language_id'],
        ];
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

    /**
     * @param string $attribute the attribute being validated
     * @param array $params values for the placeholders in the error message
     */
    public function validateTimeZone($attribute, $params)
    {
        if (!in_array($this->$attribute, timezone_identifiers_list())) {
            $this->addError($attribute, Yii::t('app', 'Time zone is not valid'));
        }
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        if (isset($this->name)) {
            return $this->name;
        }

        return $this->user->username;
    }

    /**
     * @return null|string
     */
    public function getDisplayLocation()
    {
        if (!isset($this->_displayLocation)) {
            $parts = [];
            if (isset($this->country)) {
                $parts[] = Yii::$app->geographer->getCountryName($this->country);
            }
            if (isset($this->city)) {
                $parts[] = Yii::$app->geographer->getCityName($this->city);
            }
            if (count($parts)) {
                $this->_displayLocation = implode(', ', $parts);
            }
        }

        return $this->_displayLocation;
    }

    /**
     * @return null|int
     */
    public function getAge()
    {
        if (isset($this->dob) && $this->_age == null) {
            $date = new \DateTime($this->dob);
            $now = new \DateTime();
            $interval = $now->diff($date);
            $this->_age = $interval->y;
        }

        return $this->_age;
    }

    /**
     * @return mixed|null
     */
    public function getCityName()
    {
        if (isset($this->city)) {
            return Yii::$app->geographer->getCityName($this->city);
        }

        return null;
    }

    /**
     * Returns avatar url or null if avatar is not set.
     *
     * @param int $width
     * @param int $height
     * @return null|string
     */
    public function getAvatarUrl($width = 200, $height = 200)
    {
        if (isset($this->photo_id)) {
            return $this->photo->getThumbnail($width, $height);
        }

        $this->fallbackAvatar =  '//gravatar.com/avatar/' . md5($this->user->email) . '?s=' . $width;

        $this->trigger(self::EVENT_AVATAR_FALLBACK, new ProfileEvent(['profile' => $this]));

        return $this->fallbackAvatar;
    }

    /**
     * @param bool $plural
     * @return array
     */
    public function getSexOptions($plural = false)
    {
        $sexModels = $this->getSexModels();
        $data = [self::SEX_NOT_SET => Yii::t('app', 'Not set')];
        foreach ($sexModels as $sexModel) {
            $data[$sexModel->sex] = Yii::t('app', $sexModel->getTitle($plural));
        }

        return $data;
    }

    /**
     * @return Sex[]|array|bool|mixed|\yii\db\ActiveRecord[]
     */
    public function getSexModels()
    {
        if (!isset(self::$sexModels)) {
            $sexModels = Yii::$app->cache->get(Sex::MODELS_CACHE_KEY);
            if ($sexModels === false) {
                $sexModels = Sex::find()->indexBy('sex')->all();
            }
            self::$sexModels = $sexModels;
            Yii::$app->cache->set(Sex::MODELS_CACHE_KEY, $sexModels);
        }

        return self::$sexModels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSexModel()
    {
        return $this->hasOne(Sex::class, ['sex' => 'sex']);
    }

    /**
     * @param null $value
     * @param bool $plural
     * @return mixed
     */
    public function getSexTitle($value = null, $plural = false)
    {
        $options = $this->getSexOptions($plural);
        $value = $value === null ? $this->sex : $value;
        return isset($options[$value]) ? $options[$value] : $options[self::SEX_NOT_SET];
    }

    /**
     * @param $value
     * @return bool
     */
    public function isValidSexOption($value = null)
    {
        $value = $value === null ? $this->sex : $value;
        $sexOptions = $this->getSexOptions();

        return isset($sexOptions[$value]);
    }

    /**
     * @return array
     */
    public function getStatusOptions()
    {
        return [
            self::STATUS_NOT_SET => Yii::t('app', 'Not set'),
            self::STATUS_SINGLE => Yii::t('app', '{sex, select, 2{Single} 1{Single} other{Single}}', ['sex' => $this->sex]),
            self::STATUS_SEEING_SOMEONE => Yii::t('app', 'Seeing someone'),
            self::STATUS_MARRIED => Yii::t('app', 'Married'),
            self::STATUS_OPEN_RELATIONSHIP => Yii::t('app', 'Open relationship'),
        ];
    }

    /**
     * @param null $value
     * @return mixed
     */
    public function getStatusTitle($value = null)
    {
        $options = $this->getStatusOptions();
        $value = $value === null ? $this->status : $value;
        return isset($options[$value]) ? $options[$value] : $options[self::STATUS_NOT_SET];
    }

    /**
     * @return string
     */
    public function getLookingForTitle()
    {
        $lookingFor = [];
        foreach ($this->looking_for_sex_array as $item) {
            $lookingFor[] = $this->getSexTitle($item, true);
        }

        if (count($lookingFor)) {
            return implode(', ', $lookingFor);
        } else {
            return Yii::t('app', 'Not set');
        }
    }

    /**
     * @return string
     */
    public function getLookingForAgeTitle()
    {
        if (isset($this->looking_for_from_age) && isset($this->looking_for_to_age)) {
            return sprintf("%d - %d", $this->looking_for_from_age, $this->looking_for_to_age);
        }

        if (isset($this->looking_for_from_age)) {
            return Yii::t('app', 'From {0} years', $this->looking_for_from_age);
        }

        if (isset($this->looking_for_to_age)) {
            return Yii::t('app', 'To {0} years', $this->looking_for_to_age);
        }

        return Yii::t('app', 'Not set');
    }

    /**
     * @return string|null
     */
    public function getLanguage()
    {
        if (isset($this->language_id) && $this->language_id) {
            return $this->language_id;
        }

        return null;
    }

    /**
     * @return \yii\db\ActiveQuery|Photo|null
     */
    public function getPhoto()
    {
        return $this->hasOne(Photo::class, ['id' => 'photo_id']);
    }

    /**
     * @return array
     */
    public function getExtraCategories()
    {
        if (!isset($this->_profileFieldCategories)) {
            $categories = ProfileFieldCategory::find()->visible()->sorted()->all();
            $this->_profileFieldCategories = ArrayHelper::map($categories, 'alias', function($item, $default) {
                /** @var $item  ProfileFieldCategory */
                return Yii::t($item->language_category, $item->title);
            });
        }

        return $this->_profileFieldCategories;
    }

    /**
     * @param $userId
     * @param $category
     * @return array
     */
    public function getExtraFields($userId, $category)
    {
        $data = [];
        $fields = $this->getProfileFields($category);
        $extra = $this->getProfileExtra($userId, $category);
        foreach ($fields as $field) {
            $data[$field->alias] = [
                'field' => $field,
                'extra' => isset($extra[$field->alias]) ? $extra[$field->alias] : null,
            ];
        }

        return $data;
    }

    /**
     * @param $category
     * @return ProfileField|array|mixed
     */
    protected function getProfileFields($category)
    {
        if (!isset($this->_profileFields)) {
            $data = [];
            $fields = ProfileField::find()->joinWith('category')->visible()->sorted()->all();
            foreach ($fields as $field) {
                $data[$field->category->alias][] = $field;
            }
            $this->_profileFields = $data;
        }

        return isset($this->_profileFields[$category]) ? $this->_profileFields[$category] : [];
    }

    /**
     * @param $userId
     * @param $category
     * @return ProfileExtra|array|mixed
     */
    public function getProfileExtra($userId, $category)
    {
        if (!isset($this->_profileExtra)) {
            $data = [];
            $extraItems = ProfileExtra::find()->where(['user_id' => $userId])->joinWith(['field', 'field.category'])->all();
            foreach ($extraItems as $extra) {
                $data[$extra->field->category->alias][$extra->field->alias] = $extra;
            }
            $this->_profileExtra = $data;
        }

        return isset($this->_profileExtra[$category]) ? $this->_profileExtra[$category] : [];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'timezone' => Yii::t('app', 'Time zone'),
            'dob' => Yii::t('app', 'Date of birth'),
            'sex' => Yii::t('app', 'Sex'),
            'looking_for_sex' => Yii::t('app', 'Looking for'),
            'looking_for_from_age' => Yii::t('app', 'From'),
            'looking_for_to_age' => Yii::t('app', 'To'),
            'status' => Yii::t('app', 'Status'),
            'city' => Yii::t('app', 'City'),
            'country' => Yii::t('app', 'Country'),
            'language_id' => Yii::t('app', 'Language'),
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        try {
            /** @var City $city */
            $city = City::build($this->city);
            $this->latitude = $city->getLatitude();
            $this->longitude = $city->getLongitude();
        } catch (\Exception $e) {

        }

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();

        if (isset($this->looking_for_sex)) {
            foreach (array_keys($this->getSexOptions()) as $value) {
                if ($this->looking_for_sex & $value) {
                    $this->looking_for_sex_array[$value] = $value;
                }
            }
        }
    }
}
