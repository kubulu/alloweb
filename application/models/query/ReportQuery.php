<?php

namespace app\models\query;

use app\models\Report;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class ReportQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this|ReportQuery
     */
    public function newOnly()
    {
        return $this->andWhere('report.is_viewed = 0');
    }

    /**
     * @return $this
     */
    public function latest()
    {
        return $this->orderBy('report.id desc');
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
