<?php

namespace app\settings;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\settings
 */
class SettingsAction extends \yii\base\Action
{
    /**
     * @var string
     */
    public $category;
    /**
     * @var string
     */
    public $viewFile;
    /**
     * @var array|callable
     */
    public $viewParams = [];
    /**
     * @var array
     */
    public $items;
    /**
     * @var string
     */
    public $title;

    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        $settingsManager = new SettingsManager($this->category, $this->items);
        $settingsModel = SettingsModel::createModel($this->items);
        $settingsModel->setAttributes($settingsManager->getSetting($settingsModel->getAttributes()));

        if ($settingsModel->load(Yii::$app->request->post()) && $settingsModel->validate()) {
            $settingsManager->setSetting($settingsModel->getAttributes());
            Yii::$app->session->setFlash('settings', 'Settings have been saved');
            return Yii::$app->controller->refresh();
        }

        $viewParams = is_callable($this->viewParams) ? call_user_func($this->viewParams) : $this->viewParams;
        $viewParams = array_merge($viewParams, [
            'settingsManager' => $settingsManager,
            'settingsModel' => $settingsModel,
            'items' => $this->items,
            'title' => $this->title,
        ]);

        return $this->controller->render($this->viewFile, $viewParams);
    }
}
