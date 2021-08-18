<?php

namespace app\modules\admin\controllers;

use app\models\Log;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\controllers
 */
class LogController extends \app\modules\admin\components\Controller
{
    /**
     * @var string
     */
    public $model = Log::class;

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'logDataProvider' => new ActiveDataProvider([
                'query' => Log::find(),
                'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
            ]),
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel(['id' => $id]);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Log message has been deleted'));
        }

        return $this->redirect(Yii::$app->request->referrer ?? 'index');
    }

    /**
     * @return \yii\web\Response
     */
    public function actionFlush()
    {
        Log::deleteAll();

        Yii::$app->session->setFlash('success', Yii::t('app', 'Log messages have been deleted'));
        
		return $this->redirect(['index']);
    }
}
