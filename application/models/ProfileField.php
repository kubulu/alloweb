<?php

namespace app\models;

use app\models\fields\BaseType;
use app\models\query\ProfileFieldQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\di\Instance;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property string $field_class
 * @property string $field_config
 * @property string $alias
 * @property string $title
 * @property string $language_category
 * @property int $sort_order
 * @property int $is_visible
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ProfileFieldCategory $category
 */
class ProfileField extends \yii\db\ActiveRecord
{
    const IS_HIDDEN = 0;
    const IS_VISIBLE = 1;

    /**
     * @var BaseType
     */
    protected $fieldInstance;
    /**
     * @var bool
     */
    protected $excludeFieldAttributes = false;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%profile_field}}';
    }

    /**
     * @return ProfileFieldQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new ProfileFieldQuery(get_called_class());
    }

    public function init()
    {
        parent::init();
        if (!isset($this->is_visible)) {
            $this->is_visible = true;
        }
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            ['alias', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/',
                'message' => Yii::t('app', 'Alias can only contain alphanumeric characters, underscores and dashes.'),
            ],
            [['category_id', 'field_class', 'alias', 'title'], 'required'],
            [['field_config'], 'string'],
            [['sort_order', 'is_visible', 'created_at', 'updated_at'], 'integer'],
            [['field_class', 'alias', 'title'], 'string', 'max' => 255],
            [['language_category'], 'string', 'max' => 64],
            [['language_category'], 'default', 'value' => 'app'],
            [['sort_order'], 'default', 'value' => 100],
            [['category_id', 'alias'], 'unique', 'targetAttribute' => ['category_id', 'alias']],
            [['category_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ProfileFieldCategory::class,
                'targetAttribute' => ['category_id' => 'id']
            ],
        ];

        return array_merge($rules, $this->getFieldRules());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field_class' => Yii::t('app', 'Field Class'),
            'field_config' => Yii::t('app', 'Field Config'),
            'alias' => Yii::t('app', 'Alias'),
            'title' => Yii::t('app', 'Title'),
            'language_category' => Yii::t('app', 'Language Category'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'is_visible' => Yii::t('app', 'Visible'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        if ($this->excludeFieldAttributes) {
            return parent::attributes();
        }
        return array_merge(parent::attributes(), $this->getFieldAttributes());
    }

    /**
     * @return array|string[]
     */
    public function safeAttributes()
    {
        return array_merge(parent::safeAttributes(), $this->getFieldAttributes());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ProfileFieldCategory::class, ['id' => 'category_id']);
    }

    /**
     * @return BaseType
     * @throws \yii\base\InvalidConfigException
     */
    public function getFieldInstance()
    {
        if (isset($this->fieldInstance)) {
            return $this->fieldInstance;
        }

        if (class_exists($this->field_class)) {
            Instance::ensure($this->field_class, BaseType::class);
            $this->fieldInstance = new $this->field_class;
            $this->fieldInstance->setProfileField($this);
            $this->fieldInstance->loadFieldConfig();
        }

        return $this->fieldInstance;
    }

    /**
     * @param BaseType $fieldInstance
     */
    public function setFieldInstance($fieldInstance)
    {
        $this->fieldInstance = $fieldInstance;
    }

    /**
     * @return array
     */
    public function getFieldRules()
    {
        if (isset($this->fieldInstance) && $this->fieldInstance instanceof BaseType) {
            return $this->fieldInstance->rules();
        }

        return [];
    }

    /**
     * @return array
     */
    public function getFieldAttributes()
    {
        if (isset($this->fieldInstance) && $this->fieldInstance instanceof BaseType) {
            return $this->fieldInstance->attributes();
        }

        return [];
    }

    /**
     * @param $value
     * @param bool $raw
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function formatValue($value, $raw = false)
    {
        $instance = $this->getFieldInstance();
        if ($instance !== null) {
            return $instance->formatValue($value, $raw);
        }

        return $value;
    }

    /**
     * @param bool $insert
     * @return bool|string
     */
    public function beforeSave($insert)
    {
        if (isset($this->fieldInstance) && $this->fieldInstance instanceof BaseType) {
            $this->field_config = $this->fieldInstance->getFieldConfig();
        }

        $this->excludeFieldAttributes = true;

        return parent::beforeSave($insert);
    }

    /**
     * @param null $categoryAlias
     * @return array
     */
    public static function getFields($categoryAlias = null)
    {
        $data = [];
        $query = static::find()->joinWith('category')->visible()->sorted();
        if ($categoryAlias !== null) {
            $query->andWhere(['profile_field_category.alias' => $categoryAlias]);
        }

        $fields = $query->all();
        foreach ($fields as $field) {
            $data[$field->category->alias][] = $field;
        }

        return $data;
    }
}
