<?php

namespace app\models\query;

use app\models\Profile;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class ProfileFieldCategoryQuery extends \yii\db\ActiveQuery
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

    /**
     * @return ProfileFieldCategoryQuery
     */
    public function visible()
    {
        return $this->andWhere(['profile_field_category.is_visible' => 1]);
    }

    /**
     * @return ProfileFieldCategoryQuery
     */
    public function sorted()
    {
        return $this->orderBy('profile_field_category.sort_order');
    }
}
