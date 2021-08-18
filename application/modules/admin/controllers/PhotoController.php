<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use app\modules\admin\models\Photo;
use dosamigos\grid\actions\ToggleAction;
use app\actions\GlideAction;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\controllers
 */
class PhotoController extends \app\modules\admin\components\Controller
{
    /**
     * @var string|Photo
     */
    public $model = Photo::class;

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'toggle' => [
                'class' => ToggleAction::class,
                'modelClass' => $this->model,
                'scenario' => Photo::SCENARIO_TOGGLE,
            ],
            'thumbnail' => [
                'class' => GlideAction::class,
                'imageFile' => function() {
                    $photo = $this->findModel(['id' => Yii::$app->request->get('id')]);
                    return $photo->source;
                },
            ],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'approve' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
            'cache' => [
                'class' => 'yii\filters\HttpCache',
                'only' => ['thumbnail'],
                'lastModified' => function ($action, $params) {
                    $photo = Photo::findOne(['id' => Yii::$app->request->get('id')]);
                    return $photo !== null ? $photo->updated_at : null;
                },
            ],
        ];
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $query = Photo::find()->joinWith(['userProfile', 'user']);
        $type = Yii::$app->request->get('unverified', 1);

        if (!$type == Photo::NOT_VERIFIED) {
            $query = $query->unverified();
        }

        $userId = Yii::$app->request->get('userId');
        $user = null;
        if ($userId !== null) {
            $user = $this->userManager->getUserById($userId, ['includeBanned' => true, 'allPhotos' => true]);
            if ($user == null) {
                throw new NotFoundHttpException('User not found');
            }
            $query->forUser($userId);
        }

        return $this->render('index', [
            'type' => $type,
            'user' => $user,
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
            ]),
        ]);
    }

    /**
     * @param integer $id
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionApprove($id)
    {
        /* @var $photo Photo */
        $photo = $this->findModel(['id' => $id]);
        if (!$photo->approve()) {
            throw new Exception('Could not approve photo entry');
        }

        if ($photo->user->profile->photo_id == null) {
            $this->photoManager->resetUserPhoto($photo->user_id, $photo->id);
        }

        return $this->sendJson([
            'success' => true,
            'message' => Yii::t('app', 'Photo has been approved'),
        ]);
    }

    /**
     * @param integer $id
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var $photo Photo */
        $photo = $this->findModel(['id' => $id]);
        if (!$this->photoManager->deletePhoto($photo)) {
            throw new Exception('Could not delete photo entry');
        }

        return $this->sendJson([
            'success' => true,
            'message' => Yii::t('app', 'Photo has been removed'),
        ]);
    }
}
