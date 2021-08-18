<?php

namespace youdate\widgets;

use app\helpers\Html;
use app\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Menu;

class HeaderNavigation extends Menu
{
    /**
     * @var string
     */
    public $linkTemplate = '<a class="nav-link {active}" href="{url}"><i class="fe fe-{icon}"></i>{label} {count}</a>';
    /**
     * @var string
     */
    public $countTemplate = '<span class="float-right"><span class="badge badge-primary">{count}</span></span>';
    /**
     * @var array
     */
    public $options = ['class' => 'nav nav-tabs border-0 flex-column flex-lg-row'];
    /**
     * @var array
     */
    public $itemOptions = ['class' => 'nav-item'];

    /**
     * @param array $item
     * @return string
     */
    protected function renderItem($item)
    {
        $active = $this->isItemActive($item) || isset($item['active']) && $item['active'];
        $count = ArrayHelper::getValue($item, 'count');

        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

            return strtr($template, [
                '{url}' => Html::encode(Url::to($item['url'])),
                '{label}' => $item['label'],
                '{icon}' => $item['icon'],
                '{active}' => $active ? 'active' : '',
                '{count}' => $count ? strtr($this->countTemplate, ['{count}' => $count]) : '',
            ]);
        }

        $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);

        return strtr($template, [
            '{label}' => $item['label'],
            '{icon}' => $item['icon'],
            '{active}' => $active ? 'active' : '',
            '{count}' => $count ? strtr($this->countTemplate, ['{count}' => $count]) : '',
        ]);
    }
}
