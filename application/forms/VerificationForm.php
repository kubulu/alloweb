<?php

namespace app\forms;

use app\files\Storage;
use app\models\Verification;
use Yii;
use yii\web\UploadedFile;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class VerificationForm extends \yii\base\Model
{
    /**
     * @var UploadedFile
     */
    public $photo;
    /**
     * @var integer
     */
    public $userId;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['photo'], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpeg, jpg, png'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'photo' => Yii::t('app', 'Photo'),
        ];
    }

    /**
     * @return Verification|bool
     * @throws \Exception
     * @throws \Throwable
     */
    public function createVerificationEntry()
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var Storage $photoStorage */
        $photoStorage = Yii::$app->photoStorage;

        $model = Verification::findOne(['user_id' => $this->userId]);
        if ($model == null) {
            $model = new Verification();
            $model->user_id = $this->userId;
        } else {
            $model->is_viewed = false;
            $photoStorage->delete($model->verification_photo);
        }

        $model->verification_photo = $photoStorage->save($this->photo);
        if ($model->save()) {
            return $model;
        }

        return false;
    }
}
