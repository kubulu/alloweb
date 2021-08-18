<?php

namespace app\models\query;

use app\models\Profile;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class ProfileQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Profile[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Profile|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
