<?php

namespace app\models;

use app\models\query\AccountQuery;
use app\models\query\ProfileQuery;
use app\models\query\TokenQuery;
use app\models\query\UserQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 */
class UserFinder extends \yii\base\BaseObject
{
    /**
     * @var ActiveQuery
     */
    protected $userQuery;
    /**
     * @var ActiveQuery
     */
    protected $tokenQuery;
    /**
     * @var AccountQuery
     */
    protected $accountQuery;
    /**
     * @var ActiveQuery
     */
    protected $profileQuery;

    public function init()
    {
        parent::init();
        $this->userQuery = new UserQuery(User::class);
        $this->tokenQuery = new TokenQuery(Token::class);
        $this->accountQuery = new AccountQuery(Account::class);
        $this->profileQuery = new ProfileQuery(Profile::class);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserQuery()
    {
        return $this->userQuery;
    }

    /**
     * @return ActiveQuery
     */
    public function getTokenQuery()
    {
        return $this->tokenQuery;
    }

    /**
     * @return ActiveQuery
     */
    public function getAccountQuery()
    {
        return $this->accountQuery;
    }

    /**
     * @return ActiveQuery
     */
    public function getProfileQuery()
    {
        return $this->profileQuery;
    }

    /**
     * @param int $id
     * @return User|ActiveRecord
     */
    public function findUserById($id)
    {
        return $this->findUser(['id' => $id])->one();
    }

    /**
     * @param string $username
     * @return User|ActiveRecord
     */
    public function findUserByUsername($username)
    {
        return $this->findUser(['username' => $username])->one();
    }

    /**
     * @param string $email
     * @return User|ActiveRecord
     */
    public function findUserByEmail($email)
    {
        return $this->findUser(['email' => $email])->one();
    }

    /**
     * @param string $usernameOrEmail
     * @return User|ActiveRecord
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * @param mixed $condition
     * @return \yii\db\ActiveQuery
     */
    public function findUser($condition)
    {
        return $this->userQuery->where($condition);
    }

    /**
     * @return AccountQuery
     */
    public function findAccount()
    {
        return $this->accountQuery;
    }

    /**
     * @param $id
     * @return \app\models\Account|null
     */
    public function findAccountById($id)
    {
        return $this->accountQuery->where(['id' => $id])->one();
    }

    /**
     * @param mixed $condition
     * @return ActiveQuery
     */
    public function findToken($condition)
    {
        return $this->tokenQuery->where($condition);
    }

    /**
     * @param integer $userId
     * @param string $code
     * @param integer $type
     * @return Token|array|ActiveRecord
     */
    public function findTokenByParams($userId, $code, $type)
    {
        return $this->findToken([
            'user_id' => $userId,
            'code' => $code,
            'type' => $type,
        ])->one();
    }

    /**
     * @param int $id
     * @return Profile|array|null|ActiveRecord
     */
    public function findProfileById($id)
    {
        return $this->findProfile(['user_id' => $id])->one();
    }

    /**
     * @param mixed $condition
     * @return \yii\db\ActiveQuery
     */
    public function findProfile($condition)
    {
        return $this->profileQuery->where($condition);
    }
}
