<?php

namespace app\controllers;

use app\settings\Settings;
use app\models\User;
use app\models\Upload;
use app\actions\GlideAction;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use trntv\filekit\events\UploadEvent;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class PhotoController extends \app\base\Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['upload-photo', 'upload-photo-delete', 'set-main', 'delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['thumbnail'],
                    ],

                    [
                        'allow' => false,
                        'roles' => ['*'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'set-main' => ['post'],
                    'upload-photo' => ['post'],
                    'upload-photo-delete' => ['delete'],
                    'delete' => ['post'],
                ],
            ],
            'cache' => [
                'class' => 'yii\filters\HttpCache',
                'only' => ['thumbnail'],
                'lastModified' => function ($action, $params) {
                    $photo = $this->photoManager->getPhoto(Yii::$app->request->get('id'), [
                        'verifiedOnly' => false,
                    ]);
                    if ($photo !== null && isset($photo->updated_at)) {
                        return $photo->updated_at;
                    }
                    return null;
                },
            ],
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function actions()
    {
        /** @var Settings $settings */
        $settings = Yii::$app->settings;
        $sizeMultiplier = 1024 * 1024;
        return [
            'thumbnail' => [
                'class' => GlideAction::class,
                'imageFile' => function() {
                    $photoId = Yii::$app->request->get('id');
                    $photoCached = Yii::$app->cache->get("photo{$photoId}_source");
                    if ($photoCached == null) {
                        $photo = $this->photoManager->getPhoto($photoId, [
                            'verifiedOnly' => false,
                        ]);
                        if ($photo == null) {
                            throw new NotFoundHttpException('Photo not found');
                        }
                        Yii::$app->cache->set("photo{$photoId}_source", $photo->source);
                        $photoCached = $photo->source;
                    }
                    return $photoCached;
                },
            ],
            'upload-photo' => [
                'class' => \app\actions\UploadAction::class,
                'fileStorage' => 'photoStorage',
                'deleteRoute' => '/photo/upload-photo-delete',
                'multiple' => true,
                'disableCsrf' => true,
                'validationRules' => [
                    [
                        'file', 'image',
                        'minWidth' => $settings->get('common', 'photoMinWidth', 500),
                        'minHeight' => $settings->get('common', 'photoMinHeight', 500),
                        'maxSize' => $settings->get('common', 'photoMaxFileSize', $sizeMultiplier * 10) * $sizeMultiplier,
                        'extensions' => ['jpg', 'jpeg', 'tiff'],
                    ],
                ],
                'on afterSave' => function (UploadEvent $event) {
                    $file = $event->file;
                    $upload = new Upload();
                    $upload->path = $file->getPath();
                    if (!$upload->save()) {
                        throw new \Exception('Could not save upload file info');
                    }
                },
            ],
            'upload-photo-delete' => [
                'class' => \trntv\filekit\actions\DeleteAction::class,
                'fileStorage' => 'photoStorage',
                'on afterDelete' => function (UploadEvent $event) {
                    $file = $event->file;
                    $upload = Upload::findOne(['user_id' => Yii::$app->user->id, 'path' => $file->getPath()]);
                    if ($upload && !$upload->delete()) {
                        throw new \Exception('Could not delete upload file info');
                    }
                },
            ]
        ];
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id == 'thumbnail') {
            $this->prepareData = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * @param $id
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \yii\base\ExitException
     */
    public function actionSetMain($id)
    {
        /** @var Settings $settings */
        $settings = Yii::$app->settings;
        $verifiedOnly = $settings->get('common', 'photoModerationEnabled');
        $photo = $this->getUserPhoto($id, ['verifiedOnly' => false]);

        if ($verifiedOnly && !$photo->is_verified) {
            return $this->sendJson([
                'success' => false,
                'message' => Yii::t('app', 'You\'re not allowed to set unverified photo as your main photo'),
            ]);
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;
        $profile = $user->profile;
        $profile->photo_id = $photo->id;

        if ($profile->save()) {
            return $this->sendJson([
                'success' => true,
                'message' => Yii::t('app', 'Your primary photo has been set'),
            ]);
        }

        return $this->sendJson([
            'success' => false,
            'message' => Yii::t('app', 'App error'),
            'errors' => $profile->errors,
        ]);
    }

    /**
     * @param $id
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\ExitException
     */
    public function actionDelete($id)
    {
        $photo = $this->getUserPhoto($id, ['verifiedOnly' => false]);
        $this->photoManager->deletePhoto($photo);
        $profile = $this->getCurrentUserProfile();
        if ($profile->photo_id == $id) {
            $this->photoManager->resetUserPhoto($profile->user_id);
        }

        return $this->sendJson([
            'success' => true,
            'message' => Yii::t('app', 'Photo has been deleted'),
        ]);
    }

    /**
     * @param $id
     * @param array $params
     * @return \app\models\Photo|array|null
     * @throws NotFoundHttpException
     */
    protected function getUserPhoto($id, $params = [])
    {
        $photo = $this->photoManager->getUserPhoto(Yii::$app->user->id, $id, $params);
        if ($photo == null) {
            throw new NotFoundHttpException('Photo not found');
        }

        return $photo;
    }
}
