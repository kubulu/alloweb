<?php

namespace app\forms;

use app\files\Storage;
use app\models\Photo;
use app\models\Upload;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class UploadForm extends \yii\base\Model
{
    /**
     * @var array
     */
    public $photos = [];

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['photos', 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'photos' => Yii::t('app', 'Photos'),
        ];
    }

    /**
     * @return array|bool
     * @throws \Exception
     * @throws \Throwable
     */
    public function createPhotos()
    {
        $photos = (array) $this->photos;
        if (!count($photos)) {
            return false;
        }

        /** @var Storage $photoStorage */
        $photoStorage = Yii::$app->photoStorage;
        $photoIDs = [];
        foreach ($photos as $photo) {
            if (!isset($photo['path'])) {
                break;
            }
            $uploadModel = Upload::findOne(['user_id' => Yii::$app->user->id, 'path' => $photo]);
            if ($uploadModel !== null && $photoStorage->isFileExists($photo['path'])) {
                $size = $photoStorage->getImageSize($photo['path']);
                $photoModel = new Photo();
                $photoModel->source = $photo['path'];
                $photoModel->width = $size[0];
                $photoModel->height = $size[1];
                if ($photoModel->save()) {
                    $photoIDs[] = $photoModel->id;
                } else {
                    dd($photoModel->errors);
                }
                $uploadModel->delete();
            } else {
                Yii::error('Upload file error');
            }
        }

        return $photoIDs;
    }
}
