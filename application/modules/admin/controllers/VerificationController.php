<?php

namespace app\modules\admin\controllers;

use app\actions\GlideAction;
use app\modules\admin\models\Verification;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\controllers
 */
class VerificationController extends \app\modules\admin\components\Controller
{
    /**
     * @var string|Verification
     */
    public $model = Verification::class;

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'thumbnail' => [
                'class' => GlideAction::class,
                'imageFile' => function() {
                    $verificationId = Yii::$app->request->get('id');
                    /** @var Verification $verification */
                    $verification = $this->findModel(['id' => $verificationId]);
                    if ($verification == null) {
                        throw new NotFoundHttpException('Photo not found');
                    }
                    return $verification->verification_photo;
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
                    'approve' => ['post'],
                    'reject' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Verifications index page
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Verification::find()->latest();
        $query->with(['user']);
        $query->andWhere(['is not', 'verification.verification_photo', null]);
        $type = Yii::$app->request->get('type', Verification::TYPE_NEW);

        if ($type == Verification::TYPE_NEW) {
            $query->newOnly();
        } else {
            $query->approvedOnly();
        }

        return $this->render('index', [
            'type' => $type,
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
            ]),
        ]);
    }

    /**
     * Verification approve
     *
     * @param integer $id
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionApprove($id)
    {
        /* @var $verification Verification */
        $verification = $this->findModel(['id' => $id]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $verification->is_viewed = true;
        $verification->user->profile->is_verified = true;
        $verification->user->profile->save();

        if (!$verification->save() || !$verification->user->profile->save()) {
            throw new \Exception('Could not update verification entry');
        }

        return $this->sendJson([
            'success' => true,
            'message' => Yii::t('app', 'Verification photo has been approved'),
        ]);
    }

    /**
     * Verification reject
     *
     * @param integer $id
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionReject($id)
    {
        /* @var $verification Verification */
        $verification = $this->findModel(['id' => $id]);

        Yii::$app->response->format = Response::FORMAT_JSON;

        Yii::$app->photoStorage->delete($verification->verification_photo);
        $verification->is_viewed = true;
        $verification->user->profile->is_verified = false;
        $verification->verification_photo = null;
        if (!$verification->save() || !$verification->user->profile->save()) {
            throw new \Exception('Could not update verification entry');
        }

        return $this->sendJson([
            'success' => true,
            'message' => Yii::t('app', 'Verification photo has been rejected'),
        ]);
    }
}
