<?php

namespace app\commands;

use app\models\Photo;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\imagine\Image;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\commands
 */
class PhotoController extends Controller
{
    /**
     * Strip sensitive EXIF data, process autorotate.
     */
    public function actionFixAll()
    {
        $photos = Photo::find()->all();
        $totalCount = count($photos);
        Console::startProgress(0, $totalCount, 'Photos done');
        $i = 0;
        foreach ($photos as $photo) {
            $filePath = Yii::$app->photoStorage->getAbsolutePath($photo->source);
            try {
                Image::autorotate($filePath)
                    ->strip()
                    ->save();
            } catch (\Exception $e) {
                Yii::warning($e->getMessage());
            }
            Console::updateProgress($i++, $totalCount, 'Photos done');
        }
        Console::endProgress(true);
    }
}
