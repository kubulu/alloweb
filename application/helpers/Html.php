<?php

namespace app\helpers;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\helpers
 */
class Html extends \yii\helpers\Html
{
    /**
     * @param $text
     * @return string
     */
    public static function prettyPrinted($text)
    {
        $text = self::encode($text);
        $text = preg_replace("/(\r?\n){2,}/", "\n\n", $text);
        return nl2br($text);
    }
}
