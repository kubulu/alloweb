<?php

use app\helpers\Url;
use yii\bootstrap\Nav;

/** @var $settingsManager \app\settings\SettingsManager */
/** @var $settingsModel \app\settings\SettingsModel */
/** @var $this \yii\web\View */
/** @var $title string */
/** @var $content string */

$title = Yii::t('app', 'Settings');
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => Url::current()];
?>
<div class="row">
    <div class="col-xs-12 col-sm-3">
        <div class="box box-widget">
            <div class="box-footer no-padding">
                <?= Nav::widget([
                    'options' => ['class' => 'nav nav-stacked'],
                    'items' => [
                        ['label' => Yii::t('app', 'Main settings'), 'url' => ['settings/index']],
                        ['label' => Yii::t('app', 'Photo settings'), 'url' => ['settings/photo']],
                        ['label' => Yii::t('app', 'Payment settings'), 'url' => ['settings/payment']],
                        ['label' => Yii::t('app', 'Price settings'), 'url' => ['settings/prices']],
                        ['label' => Yii::t('app', 'Sex/Gender settings'), 'url' => ['settings/genders']],
                        ['label' => Yii::t('app', 'Cached data'), 'url' => ['settings/cached-data']],
                    ]
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-9">
        <?= $content ?>
    </div>
</div>
