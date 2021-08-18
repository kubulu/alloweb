<?php

namespace app\modules\admin\models;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\models
 */
class Verification extends \app\models\Verification
{
    const TYPE_NEW = 'new';
    const TYPE_APPROVED = 'approved';

    const SCENARIO_TOGGLE = 'toggle';
    const IS_NEW = 0;
    const IS_VIEWED = 1;

    /**
     * @return array
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_TOGGLE => ['is_viewed'],
        ]);
    }
}
