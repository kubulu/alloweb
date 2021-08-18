<?php

namespace youdate\widgets;

use yii\widgets\Menu;
use yii\helpers\ArrayHelper;
use app\helpers\Html;
use app\helpers\Url;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class Sidebar extends Menu
{
    /**
     * @var string
     */
    public $header;
    /**
     * @var array
     */
    public $headerOptions = ['tag' => 'h3', 'class' => 'page-title mb-5 d-flex justify-content-between align-items-center'];
    /**
     * @var string
     */
    public $countTemplate = '<span class="float-right"><span class="badge badge-{countClass}">{count}</span></span>';
    /**
     * @var string
     */
    public $linkTemplate = '<a class="list-group-item list-group-item-action {active}" href="{url}"> 
            <span class="icon mr-3"><i class="fe fe-{icon}"></i></span>{label} {count}</a>';
    /**
     * @var array
     */
    public $options = [
        'tag' => 'div',
        'class' => 'sidebar-menu list-group list-group-transparent mb-0 d-none d-lg-block',
    ];
    /**
     * @var array
     */
    public $itemOptions = [
        'class' => '',
        'tag' => null,
    ];

    /**
     * @return string|void
     */
    public function run()
    {
        if (isset($this->header)) {
            $tag = ArrayHelper::remove($this->headerOptions, 'tag');
            $header = $this->header .
                Html::tag('div', '', ['class' => 'd-flex d-lg-none flex-fill sidebar-menu-line']) .
                Html::button('<i class="fe fe-chevron-down"></i>', [
                    'data-icon-show' => 'fe-chevron-down',
                    'data-icon-hide' => 'fe-chevron-up',
                    'class' => 'btn btn-sm btn-secondary btn-toggle-sidebar float-right d-block d-lg-none',
                ]);
            echo Html::tag($tag, $header, $this->headerOptions);
        }

        parent::run();
    }

    /**
     * @param array $item
     * @return string
     */
    protected function renderItem($item)
    {
        $active = $this->isItemActive($item);
        $count = ArrayHelper::getValue($item, 'count');
        $icon = ArrayHelper::getValue($item, 'icon');

        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

            return strtr($template, [
                '{url}' => Html::encode(Url::to($item['url'])),
                '{label}' => $item['label'],
                '{icon}' => $icon,
                '{active}' => $active ? 'active' : '',
                '{count}' => $count ? strtr($this->countTemplate, [
                    '{count}' => $count,
                    '{countClass}' => ($active ? 'primary' : 'secondary'),
                ]) : '',
            ]);
        }

        $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);

        return strtr($template, [
            '{label}' => $item['label'],
            '{icon}' => $icon,
            '{active}' => $active ? 'active' : '',
            '{count}' => $count ? strtr($this->countTemplate, [
                '{count}' => $count,
                '{countClass}' => ($active ? 'primary' : 'secondary'),
            ]) : '',
        ]);
    }
}
