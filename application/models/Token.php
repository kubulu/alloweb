<?php

namespace app\models;

use app\models\query\TokenQuery;
use Yii;
use yii\helpers\Url;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 * @property integer $user_id
 * @property string $code
 * @property integer $created_at
 * @property integer $type
 * @property string $url
 * @property bool $isExpired
 * @property User $user
 */
class Token extends \yii\db\ActiveRecord
{
    const TYPE_CONFIRMATION = 0;
    const TYPE_RECOVERY = 1;
    const TYPE_CONFIRM_NEW_EMAIL = 2;
    const TYPE_CONFIRM_OLD_EMAIL = 3;

    /**
     * @var int
     */
    public $confirmWithin = 86400; // 24 hours
    /**
     * @var int
     */
    public $recoverWithin = 21600; // 6 hours

    /**
     * @return TokenQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new TokenQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        switch ($this->type) {
            case self::TYPE_CONFIRMATION:
                $route = '/registration/confirm';
                break;
            case self::TYPE_RECOVERY:
                $route = '/recovery/reset';
                break;
            case self::TYPE_CONFIRM_NEW_EMAIL:
            case self::TYPE_CONFIRM_OLD_EMAIL:
                $route = '/settings/confirm';
                break;
            default:
                throw new \RuntimeException();
        }

        return Url::to([$route, 'id' => $this->user_id, 'code' => $this->code], true);
    }

    /**
     * @return bool
     */
    public function getIsExpired()
    {
        switch ($this->type) {
            case self::TYPE_CONFIRMATION:
            case self::TYPE_CONFIRM_NEW_EMAIL:
            case self::TYPE_CONFIRM_OLD_EMAIL:
                $expirationTime = $this->confirmWithin;
                break;
            case self::TYPE_RECOVERY:
                $expirationTime = $this->recoverWithin;
                break;
            default:
                throw new \RuntimeException();
        }

        return ($this->created_at + $expirationTime) < time();
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            static::deleteAll(['user_id' => $this->user_id, 'type' => $this->type]);
            $this->setAttribute('created_at', time());
            $this->setAttribute('code', Yii::$app->security->generateRandomString());
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%token}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'type'], 'integer'],
            ['code', 'safe'],
        ];
    }

    /**
     * @return array|string[]
     */
    public static function primaryKey()
    {
        return ['user_id', 'code', 'type'];
    }
}
