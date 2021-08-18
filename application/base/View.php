<?php

namespace app\base;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\base
 */
class View extends \yii\web\View
{
    const EVENT_CUSTOM_HEADER = 'customHeader';
    const EVENT_CUSTOM_FOOTER = 'customFooter';

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function frontendSetting($key, $default = null)
    {
        if (!isset($this->params['frontend'])) {
            return $default;
        }

        return ArrayHelper::getValue($this->params['frontend'], $key, $default);
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function themeSetting($key, $default = null)
    {
        if (!isset($this->params['theme'])) {
            return $default;
        }

        return ArrayHelper::getValue($this->params['theme'], $key, $default);
    }

    /**
     * @param string $viewFile
     * @param array $params
     * @param null $context
     * @return string
     */
    public function renderFile($viewFile, $params = [], $context = null)
    {
        if (!Yii::$app->themeManager->isExtendedTheme) {
            return parent::renderFile($viewFile, $params, $context);
        }

        $extendedViewFile = str_replace('@theme', '@extendedTheme', $viewFile);
        if (is_file(Yii::getAlias($extendedViewFile))) {
            $viewFile = $extendedViewFile;
        }

        return parent::renderFile($viewFile, $params, $context);
    }

    public function customHeaderCode()
    {
        $this->trigger(self::EVENT_CUSTOM_HEADER);
    }

    public function customFooterCode()
    {
        $this->trigger(self::EVENT_CUSTOM_FOOTER);
    }
}
