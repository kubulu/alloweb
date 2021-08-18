<?php

namespace app\models\query;

use app\models\Account;
use yii\authclient\ClientInterface;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 * @method Account|null one($db = null)
 * @method Account[]    all($db = null)
 */
class AccountQuery extends \yii\db\ActiveQuery
{
    /**
     * @param $code string
     * @return $this
     */
    public function byCode($code)
    {
        return $this->andWhere(['code' => md5($code)]);
    }

    /**
     * @param $id
     * @return $this
     */
    public function byId($id)
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param $userId
     * @return $this
     */
    public function byUser($userId)
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    /**
     * @param ClientInterface $client
     * @return $this
     */
    public function byClient(ClientInterface $client)
    {
        return $this->andWhere([
            'provider'  => $client->getId(),
            'client_id' => $client->getUserAttributes()['id'],
        ]);
    }
}
