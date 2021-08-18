<?php

namespace youdate\widgets;

use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class GridView extends \yii\grid\GridView
{
    /**
     * @var string
     */
    public $emptyView;
    /**
     * @var array
     */
    public $emptyViewParams = [];

    public function init()
    {
        parent::init();
        $this->pager = ArrayHelper::merge([
            'options' => ['class' => 'pagination m-auto pt-4 pb-4 clearfix'],
            'pageCssClass' => 'page-item',
            'firstPageCssClass' => 'page-item',
            'lastPageCssClass' => 'page-item',
            'prevPageCssClass' => 'page-item',
            'nextPageCssClass' => 'page-item',
            'linkOptions' => [
                'class' => 'page-link'
            ],
            'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link', 'disabled' => 'disabled'],
        ], $this->pager);
    }

    /**
     * @return string
     */
    public function renderEmpty()
    {
        if (isset($this->emptyView)) {
            return $this->getView()->render($this->emptyView, $this->emptyViewParams);
        }

        return parent::renderEmpty();
    }
}
