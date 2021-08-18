<?php

namespace youdate\widgets;

use yii\base\Widget;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class UserMenu extends Widget
{
    /**
     * @var bool
     */
    public $showBalance = true;

    public function run()
    {
        return $this->render('user-menu/widget', [
            'showBalance' => $this->showBalance,
        ]);
    }
}
