<?php

namespace app\models\query;

use app\models\Token;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class TokenQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Token[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Token|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
