<?php

namespace youdate\widgets;

use app\helpers\Html;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class Gallery extends \dosamigos\gallery\Gallery
{
    public $maxPhotosVisible = 3;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!empty($this->items)) {
            echo $this->renderItems();
        }
        echo $this->renderTemplate();
        $this->registerClientScript();
    }

    /**
     * Renders the template to display the images on a lightbox
     * @return string the template
     */
    public function renderTemplate()
    {
        $template[] = '<div class="slides"></div>';
        $template[] = '<h3 class="title"></h3>';
        $template[] = '<a class="prev text-light">‹</a>';
        $template[] = '<a class="next text-light">›</a>';
        $template[] = '<a class="close text-light"></a>';
        $template[] = '<a class="play-pause"></a>';
        $template[] = '<ol class="indicator"></ol>';
        return Html::tag('div', implode("\n", $template), $this->templateOptions);
    }

    /**
     * @return string the items that are need to be rendered.
     */
    public function renderItems()
    {
        $items = [];
        $counter = 1;
        $totalCount = count($this->items);
        $hiddenCount = $totalCount - $this->maxPhotosVisible;

        foreach ($this->items as $item) {
            $items[] = $this->renderItem($item,
                $counter > $this->maxPhotosVisible,
                 $counter == $this->maxPhotosVisible ? $hiddenCount : null
            );
            $counter++;
        }
        return Html::tag('div', implode("\n", array_filter($items)), $this->options);
    }

    /**
     * @param mixed $item
     * @param bool $hidden
     * @param null $hiddenCount
     * @return null|string the item to render
     */
    public function renderItem($item, $hidden = false, $hiddenCount = null)
    {
        if (is_string($item)) {
            return Html::a(Html::img($item), $item, ['class' => 'gallery-item']);
        }
        $src = ArrayHelper::getValue($item, 'src');
        if ($src === null) {
            return null;
        }
        $url = ArrayHelper::getValue($item, 'url', $src);
        $options = ArrayHelper::getValue($item, 'options', []);
        $imageOptions = ArrayHelper::getValue($item, 'imageOptions', []);
        $wrapperOptions = ArrayHelper::getValue($item, 'wrapperOptions', []);
        $hiddenPhotosOptions = ArrayHelper::getValue($item, 'hiddenPhotosOptions', []);
        Html::addCssClass($wrapperOptions, 'gallery-item');
        Html::addCssClass($hiddenPhotosOptions, 'hidden-photos');
        if ($hidden === true) {
            Html::addCssClass($options, 'hidden');
        }

        $contents = Html::img($src, $imageOptions);
        if ($hiddenCount) {
            $contents .= Html::tag('div',
                Html::tag('span', Yii::t('youdate', '+{0} photos', $hiddenCount)),
                $hiddenPhotosOptions
            );
        }

        $contents = Html::tag('div', $contents, $wrapperOptions);
        return Html::a($contents, $url, $options);
    }
}
