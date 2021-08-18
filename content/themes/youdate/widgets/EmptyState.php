<?php

namespace youdate\widgets;

use app\helpers\Html;
use Yii;
use yii\base\Widget;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class EmptyState extends Widget
{
    /**
     * @var string
     */
    public $icon;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $subTitle;
    /**
     * @var string
     */
    public $action;
    /**
     * @var array
     */
    public $options = [];

    public function init()
    {
        parent::init();
        if (!isset($this->title)) {
            $this->title = Yii::t('youdate', 'Nothing to display');
        }
    }

    public function run()
    {
        Html::addCssClass($this->options, 'empty-state');

        return $this->render('empty-state', [
            'options' => $this->options,
            'icon' => $this->icon,
            'title' => $this->title,
            'subTitle' => $this->subTitle,
            'action' => $this->action,
        ]);
    }
}
