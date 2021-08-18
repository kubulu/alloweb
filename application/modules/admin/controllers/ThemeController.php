<?php

namespace app\modules\admin\controllers;

use Yii;
use app\settings\SettingsAction;
use app\modules\admin\components\Controller;
use app\themes\ThemeManager;
use yii\filters\VerbFilter;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\controllers
 */
class ThemeController extends Controller
{
    /**
     * @var ThemeManager
     */
    public $themeManager;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'activate' => ['post'],
            ],
        ];

        return $behaviors;
    }

    public function init()
    {
        parent::init();
        $this->themeManager = Yii::$app->themeManager;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function actions()
    {
        $actions['settings'] = [
            'class' => SettingsAction::class,
            'category' => "theme.{$this->themeManager->getCurrentThemeId()}",
            'title' => Yii::t('app', 'Theme settings'),
            'viewFile' => 'settings',
            'items' => $this->themeManager->getThemeSettings(),
        ];

        return $actions;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'themes' => $this->themeManager->getAvailableThemes(),
            'currentThemeId' => $this->themeManager->getCurrentThemeId(),
            'currentThemeInfo' => $this->themeManager->getCurrentThemeInfo(),
        ]);
    }

    /**
     * @param $themeId
     * @return \yii\web\Response
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function actionActivate($themeId)
    {
        if ($this->themeManager->activate($themeId)) {
            Yii::$app->session->setFlash('success', 'Theme activated.');
        }

        return $this->redirect('index');
    }
}
