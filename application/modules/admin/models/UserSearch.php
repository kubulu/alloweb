<?php

namespace app\modules\admin\models;

use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\models
 */
class UserSearch extends \yii\base\Model
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $created_at;
    /**
     * @var int
     */
    public $last_login_at;
    /**
     * @var int
     */
    public $registration_ip;
    /**
     * @var bool
     */
    public $is_verified;
    /**
     * @var bool
     */
    public $is_premium;

    /** @inheritdoc */
    public function rules()
    {
        return [
            'fieldsSafe' => [['id', 'username', 'email', 'registration_ip', 'created_at', 'last_login_at'], 'safe'],
            'createdDefault' => ['created_at', 'default', 'value' => null],
            'lastLoginDefault' => ['last_login_at', 'default', 'value' => null],
            'flags' => [['is_verified', 'is_premium'], 'boolean', 'strict' => true],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '#'),
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'created_at' => Yii::t('app', 'Registration time'),
            'last_login_at' => Yii::t('app', 'Last login'),
            'registration_ip' => Yii::t('app', 'Registration IP'),
            'is_verified' => Yii::t('app', 'Verified'),
            'is_premium' => Yii::t('app', 'Premium'),
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find()->joinWith([
            'profile',
            'premium',
            'profile.photo',
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);
        

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->created_at !== null) {
            $date = strtotime($this->created_at);
            $query->andFilterWhere(['between', 'user.created_at', $date, $date + 3600 * 24]);
        }

        $query->andFilterWhere(['like', 'user.email', $this->email])
            ->andFilterWhere(['user.id' => $this->id])
            ->andFilterWhere(['user.registration_ip' => $this->registration_ip])
            ->andFilterWhere(['profile.is_verified' => $this->is_verified])
            ->andFilterWhere(['or',
                ['like', 'user.username', $this->username],
                ['like', 'profile.name', $this->username],
            ]);


        if (isset($this->is_premium) && $this->is_premium !== '') {
            if ($this->is_premium == true) {
                $query->andWhere('user_premium.premium_until > unix_timestamp()');
            } else {
                $query->andWhere('user_premium.premium_until IS NULL OR user_premium.premium_until < unix_timestamp()');
            }
        }

        return $dataProvider;
    }
}
