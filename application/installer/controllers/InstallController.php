<?php

namespace installer\controllers;

use app\components\AppState;
use installer\components\Requirements;
use installer\forms\ConfigForm;
use installer\forms\DatabaseForm;
use Yii;
use yii\web\Controller;
use yii\web\ErrorAction;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package installer\controllers
 */
class InstallController extends Controller
{
    const STEP_REQUIREMENTS = 'requirements';
    const STEP_DATABASE = 'database';
    const STEP_CONFIG = 'config';
    const SESSION_STEP_KEY = 'installerStep';
    const SESSION_DB_DATA_KEY = 'installerDbData';

    public $layout = 'installer';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $step = Yii::$app->session->get(self::SESSION_STEP_KEY, self::STEP_REQUIREMENTS);
        switch ($step) {
            case self::STEP_REQUIREMENTS:
                return $this->requirements();
                break;
            case self::STEP_DATABASE:
                return $this->database();
                break;
            case self::STEP_CONFIG:
                return $this->config();
                break;
        }

        return $this->reset();
    }

    /**
     * @return \yii\web\Response
     */
    public function actionReset()
    {
        return $this->reset();
    }

    /**
     * @return string|\yii\web\Response
     */
    protected function requirements()
    {
        if (Yii::$app->request->isPost) {
            Yii::$app->session->set(self::SESSION_STEP_KEY, self::STEP_DATABASE);
            return $this->refresh();
        }

        return $this->render('requirements', [
            'requirements' => Requirements::checkRequirements(),
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    protected function database()
    {
        $model = new DatabaseForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->setup()) {
            Yii::$app->session->set(self::SESSION_DB_DATA_KEY, $model->getAttributes());
            try {
                $dumpFile = Yii::getAlias('@app/data/dump.sql');
                $sql = file_get_contents($dumpFile);
                Yii::$app->db->createCommand($sql)->execute();
                Yii::$app->session->set(self::SESSION_STEP_KEY, self::STEP_CONFIG);
                return $this->refresh();
            } catch (\yii\db\Exception $exception) {
                $model->addError('server', $exception->getMessage());
            }
        }

        return $this->render('database', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    protected function config()
    {
        $envTemplateFile = Yii::getAlias('@app/data/.env-example');
        $model = new ConfigForm();

        $dbData = Yii::$app->session->get(self::SESSION_DB_DATA_KEY);
        $dbModel = new DatabaseForm();
        $dbModel->setAttributes($dbData);
        $dbModel->setup();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->makeWritable();
            $model->createUser();
            $model->setAttributes($dbData);
            $model->setTemplate(file_get_contents($envTemplateFile));

            $appState = new AppState(['appStateFile' => Yii::$app->params['basePath'] . '/application/runtime/application.json']);
            $appState->resetState();

            file_put_contents(Yii::$app->params['basePath'] . '/.env', $model->getConfig());
            Yii::$app->session->remove(self::SESSION_STEP_KEY);
            Yii::$app->session->remove(self::SESSION_DB_DATA_KEY);

            return $this->refresh();
        }

        return $this->render('config', [
            'model' => $model,
        ]);
    }

    /**
     * @return \yii\web\Response
     */
    protected function reset()
    {
        Yii::$app->session->set(self::SESSION_STEP_KEY, self::STEP_REQUIREMENTS);
        return $this->redirect(['index']);
    }
}
