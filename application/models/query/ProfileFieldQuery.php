<?php

namespace app\models\query;

use app\models\ProfileField;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class ProfileFieldQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return ProfileField[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ProfileField|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return ProfileFieldQuery
     */
    public function visible()
    {
        return $this->andWhere(['profile_field.is_visible' => 1]);
    }

    /**
     * @return ProfileFieldQuery
     */
    public function sorted()
    {
        return $this->orderBy('profile_field.sort_order');
    }
}
