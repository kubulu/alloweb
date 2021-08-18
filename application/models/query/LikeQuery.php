<?php

namespace app\models\query;

use app\models\Like;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class LikeQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Like[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Like|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $fromUserId
     * @param $toUserId
     * @return $this
     */
    public function whereUsersAre($fromUserId, $toUserId)
    {
        return $this->andWhere([
            'like.from_user_id' => (int) $fromUserId,
            'like.to_user_id' => (int) $toUserId,
        ]);
    }
}
