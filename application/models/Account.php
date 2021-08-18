<?php

namespace app\models;

use app\clients\ClientInterface;
use app\models\query\AccountQuery;
use Yii;
use yii\authclient\ClientInterface as BaseClientInterface;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 * @property integer $id
 * @property integer $user_id
 * @property string $provider
 * @property string $client_id
 * @property string $data
 * @property string $decodedData
 * @property string $code
 * @property integer $created_at
 * @property string $email
 * @property string $username
 *
 * @property User $user
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * @var \app\models\UserFinder
     */
    protected static $finder;
    /**
     * @var mixed
     */
    private $_data;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%social_account}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return bool
     */
    public function getIsConnected()
    {
        return $this->user_id != null;
    }

    /**
     * @return mixed
     */
    public function getDecodedData()
    {
        if ($this->_data == null) {
            $this->_data = Json::decode($this->data);
        }

        return $this->_data;
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function getConnectUrl()
    {
        $code = Yii::$app->security->generateRandomString();
        $this->updateAttributes(['code' => md5($code)]);

        return Url::to(['/registration/connect', 'code' => $code]);
    }

    /**
     * @param User $user
     * @return int
     */
    public function connect(User $user)
    {
        return $this->updateAttributes([
            'username' => null,
            'email' => null,
            'code' => null,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @return object|\yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(AccountQuery::class, [get_called_class()]);
    }

    /**
     * @param BaseClientInterface $client
     * @return Account
     * @throws \yii\base\InvalidConfigException
     */
    public static function create(BaseClientInterface $client)
    {
        /** @var Account $account */
        $account = Yii::createObject([
            'class' => static::class,
            'provider' => $client->getId(),
            'client_id' => $client->getUserAttributes()['id'],
            'data' => Json::encode($client->getUserAttributes()),
        ]);

        if ($client instanceof ClientInterface) {
            $account->setAttributes([
                'username' => $client->getUsername(),
                'email' => $client->getEmail(),
            ], false);
        }

        if (($user = static::fetchUser($account)) instanceof User) {
            $account->user_id = $user->id;
        }

        $account->save(false);

        return $account;
    }

    /**
     * @param BaseClientInterface $client
     * @throws \yii\base\InvalidConfigException
     */
    public static function connectWithUser(BaseClientInterface $client)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Something went wrong'));

            return;
        }

        $account = static::fetchAccount($client);

        if ($account->user === null) {
            $account->link('user', Yii::$app->user->identity);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Your account has been connected'));
        } else {
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('app', 'This account has already been connected to another user')
            );
        }
    }

    /**
     * @param BaseClientInterface $client
     * @return Account
     * @throws \yii\base\InvalidConfigException
     */
    protected static function fetchAccount(BaseClientInterface $client)
    {
        $account = static::getFinder()->findAccount()->byClient($client)->one();

        if (null === $account) {
            $account = Yii::createObject([
                'class' => static::class,
                'provider' => $client->getId(),
                'client_id' => $client->getUserAttributes()['id'],
                'data' => Json::encode($client->getUserAttributes()),
            ]);
            $account->save(false);
        }

        return $account;
    }

    /**
     * @param Account $account
     * @return User|bool|object|ActiveRecord
     * @throws \yii\base\InvalidConfigException
     */
    protected static function fetchUser(Account $account)
    {
        $user = static::getFinder()->findUserByEmail($account->email);

        if (null !== $user) {
            return $user;
        }

        $user = Yii::createObject([
            'class' => User::class,
            'scenario' => 'connect',
            'username' => $account->username,
            'email' => $account->email,
        ]);

        if (!$user->validate(['email'])) {
            $account->email = null;
        }

        if (!$user->validate(['username'])) {
            $account->username = null;
        }

        return $user->create() ? $user : false;
    }

    /**
     * @return UserFinder
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected static function getFinder()
    {
        if (static::$finder === null) {
            static::$finder = Yii::$container->get(UserFinder::class);
        }

        return static::$finder;
    }
}
