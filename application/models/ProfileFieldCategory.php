<?php

namespace app\models;

use app\models\query\ProfileFieldCategoryQuery;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property string $alias
 * @property string $title
 * @property string $language_category
 * @property int $sort_order
 * @property int $is_visible
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ProfileField[] $profileFields
 */
class ProfileFieldCategory extends \yii\db\ActiveRecord
{
    const IS_HIDDEN = 0;
    const IS_VISIBLE = 1;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%profile_field_category}}';
    }

    /**
     * @return ProfileFieldCategoryQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new ProfileFieldCategoryQuery(get_called_class());
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
        return [
            ['alias', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/',
                'message' => Yii::t('app', 'Alias can only contain alphanumeric characters, underscores and dashes.'),
            ],
            [['alias', 'title'], 'required'],
            [['sort_order', 'is_visible', 'created_at', 'updated_at'], 'integer'],
            [['alias', 'title'], 'string', 'max' => 255],
            [['language_category'], 'string', 'max' => 64],
            [['language_category'], 'default', 'value' => 'app'],
            [['sort_order'], 'default', 'value' => 100],
            [['alias'], 'unique'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
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
     * @return \yii\db\ActiveQuery
     */
    public function getProfileFields()
    {
        return $this->hasMany(ProfileField::class, ['category_id' => 'id']);
    }
}
