<?php

namespace app\components;

use Yii;
use yii\helpers\FileHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\components
 */
class Maintenance
{
    public static function flushData()
    {
        // flush cache
        Yii::$app->cache->flush();

        // remove published assets
        $path = Yii::getAlias(Yii::$app->assetManager->basePath);
        foreach (FileHelper::findDirectories($path) as $directory) {
            FileHelper::removeDirectory($directory);
        }
    }
}
