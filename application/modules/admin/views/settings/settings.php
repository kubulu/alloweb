<?php

use app\helpers\Url;
use app\settings\SettingsForm;

/** @var $settingsManager \app\settings\SettingsManager */
/** @var $settingsModel \app\settings\SettingsModel */
/** @var $this \yii\web\View */
/** @var $title string */

$title = Yii::t('app', 'Settings');
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => Url::current()];

$this->beginContent('@app/modules/admin/views/settings/_layout.php');

echo SettingsForm::widget([
    'manager' => $settingsManager,
    'model' => $settingsModel,
    'formView' => '@app/modules/admin/views/partials/settings_form',
    'title' => $title,
]);

$this->endContent();
