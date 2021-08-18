<?php

namespace app\models\query;

use app\models\Photo;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class PhotoQuery extends \yii\db\ActiveQuery
{
    /**
     * @param bool $verificationRequired
     * @return $this|PhotoQuery
     */
    public function verified($verificationRequired = true)
    {
        return $verificationRequired == true ? $this->andWhere('photo.is_verified = 1') : $this;
    }

    /**
     * @return $this|PhotoQuery
     */
    public function unverified()
    {
        return $this->andWhere('photo.is_verified = 0');
    }

    /**
     * @param $userId
     * @return $this
     */
    public function forUser($userId)
    {
        return $this->andWhere(['photo.user_id' => $userId]);
    }

    /**
     * @return $this
     */
    public function latest()
    {
        return $this
            ->orderBy('photo.id desc');
    }

    /**
     * @inheritdoc
     * @return Photo[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Photo|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
