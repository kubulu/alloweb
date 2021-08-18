<?php

namespace app\models\query;

use app\models\Guest;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class GuestQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Guest[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Guest|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $fromUserId
     * @return $this
     */
    public function byUser($fromUserId)
    {
        return $this->andWhere([
            'guest.from_user_id' => (int) $fromUserId,
        ]);
    }

    /**
     * @param $visitedUserId
     * @return $this
     */
    public function forUser($visitedUserId)
    {
        return $this->andWhere([
            'guest.visited_user_id' => (int) $visitedUserId,
        ]);
    }
}
