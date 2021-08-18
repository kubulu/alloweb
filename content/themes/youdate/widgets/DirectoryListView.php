<?php

namespace youdate\widgets;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class DirectoryListView extends ListView
{
    public function init()
    {
        parent::init();
        $this->options['class'] = 'list-view directory-list-view';
        $this->layout = '<div class="row row-cards row-deck">{items}</div><div class="pager">{pager}</div>';
    }
}
