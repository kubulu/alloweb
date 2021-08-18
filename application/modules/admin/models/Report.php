<?php

namespace app\modules\admin\models;

use app\helpers\Html;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\models
 */
class Report extends \app\models\Report
{
    const TYPE_NEW = 'new';
    const TYPE_VIEWED = 'all';

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

    /**
     * @return string
     */
    public function getReasonLabel()
    {
        $class = 'default';
        switch ($this->reason) {
            case self::REASON_SCAM:
            case self::REASON_SPAM:
            case self::REASON_FAKE:
                $class = 'danger';
                break;
            case self::REASON_RUDE:
            case self::REASON_BAD_PROFILE:
                $class = 'warning';
                break;
        }

        return Html::tag('span', $this->reason, ['class' => 'label label-' . $class]);
    }
}
