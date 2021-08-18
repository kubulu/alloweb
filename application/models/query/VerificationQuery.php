<?php

namespace app\models\query;

use app\models\Report;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class VerificationQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this|ReportQuery
     */
    public function newOnly()
    {
        return $this->andWhere('verification.is_viewed = 0');
    }

    /**
     * @return $this|ReportQuery
     */
    public function approvedOnly()
    {
        return $this->andWhere('verification.is_viewed = 1');
    }

    /**
     * @return $this
     */
    public function latest()
    {
        return $this->orderBy('verification.id desc');
    }

    /**
     * @inheritdoc
     * @return Report[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Report|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
