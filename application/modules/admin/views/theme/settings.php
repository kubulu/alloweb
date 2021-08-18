<?php

/** @var $settingsManager \app\settings\SettingsManager */
/** @var $settingsModel \app\settings\SettingsModel */
/** @var $this \yii\web\View */
/** @var $themeInfo array */

use app\settings\SettingsForm;

$this->title = isset($themeInfo['title']) ? $themeInfo['title'] : Yii::t('app', 'Theme');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Themes'), 'url' => ['index']];

echo SettingsForm::widget([
    'manager' => $settingsManager,
    'model' => $settingsModel,
    'formView' => '@app/modules/admin/views/partials/settings_form',
    'title' => $title,
]);
