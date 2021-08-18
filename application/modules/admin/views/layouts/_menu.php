<?php

use yii\helpers\Html;

?>

<div class="user-panel">
    <div class="pull-left image">
        <?= Html::img(Yii::$app->user->identity->profile->getAvatarUrl(45, 45), [
            'class' => 'img-rounded',
            'alt' => Yii::$app->user->identity->username,
        ]) ?>
    </div>
    <div class="pull-left info">
        <p><?= Html::encode(Yii::$app->user->identity->username) ?></p>
        <p><small class="text-muted">admin</small></p>
    </div>
</div>

<?= \app\modules\admin\widgets\Menu::widget([
    'options' => ['class' => 'sidebar-menu'],
    'items' => [
        [
            'url' => ['default/index'],
            'icon' => 'fa fa-cog',
            'label' => Yii::t('app', 'Dashboard'),
            'active' => ($this->context instanceof app\modules\admin\controllers\DefaultController),
        ],
        [
            'label' => Yii::t('app', 'Content'),
            'options' => ['class' => 'header'],
        ],
        [
            'url' => ['user/index'],
            'icon' => 'fa fa-users',
            'label' => Yii::t('app', 'Users'),
            'active' => ($this->context instanceof app\modules\admin\controllers\UserController),
        ],
        [
            'url' => ['photo/index'],
            'icon' => 'fa fa-photo',
            'label' => Yii::t('app', 'Photos'),
            'active' => ($this->context instanceof app\modules\admin\controllers\PhotoController),
        ],
        [
            'url' => ['page/index'],
            'icon' => 'fa fa-file',
            'label' => Yii::t('app', 'Pages'),
            'active' => ($this->context instanceof app\modules\admin\controllers\PageController),
        ],
        [
            'url' => ['language/list'],
            'icon' => 'fa fa-globe',
            'label' => Yii::t('app', 'Languages'),
            'active' => ($this->context instanceof app\modules\admin\controllers\LanguageController),
        ],
        [
            'url' => ['report/index'],
            'icon' => 'fa fa-flag',
            'label' => Yii::t('app', 'Reports'),
            'active' => ($this->context instanceof app\modules\admin\controllers\ReportController),
        ],
        [
            'url' => ['verification/index'],
            'icon' => 'fa fa-check-circle',
            'label' => Yii::t('app', 'Verifications'),
            'active' => ($this->context instanceof app\modules\admin\controllers\VerificationController),
        ],
        [
            'label' => Yii::t('app', 'System'),
            'options' => ['class' => 'header'],
        ],
        [
            'url' => ['settings/index'],
            'icon' => 'fa fa-cog',
            'label' => Yii::t('app', 'Settings'),
            'active' => ($this->context instanceof app\modules\admin\controllers\SettingsController),
        ],
        [
            'url' => ['log/index'],
            'icon' => 'fa fa-file-text-o',
            'label' => Yii::t('app', 'Logs'),
            'active' => ($this->context instanceof app\modules\admin\controllers\LogController),
        ],
        [
            'url' => ['profile-field/index'],
            'icon' => 'fa fa-vcard',
            'label' => Yii::t('app', 'Profile fields'),
            'active' => (
                $this->context instanceof app\modules\admin\controllers\ProfileFieldCategoryController ||
                $this->context instanceof app\modules\admin\controllers\ProfileFieldController
            ),
            'items' => [
                [
                    'url' => ['profile-field/index'],
                    'icon' => 'fa fa-circle-o',
                    'label' => Yii::t('app', 'Profile fields'),
                ],
                [
                    'url' => ['profile-field-category/index'],
                    'icon' => 'fa fa-circle-o',
                    'label' => Yii::t('app', 'Field categories'),
                ],
            ]
        ],
        [
            'url' => ['theme/index'],
            'icon' => 'fa fa-file-code-o',
            'label' => Yii::t('app', 'Themes'),
            'active' => ($this->context instanceof app\modules\admin\controllers\ThemeController),
            'items' => [
                [
                    'url' => ['theme/index'],
                    'icon' => 'fa fa-circle-o',
                    'label' => Yii::t('app', 'Catalog'),
                ],
                [
                    'url' => ['theme/settings'],
                    'icon' => 'fa fa-circle-o',
                    'label' => Yii::t('app', 'Theme settings'),
                ],
            ]
        ],
    ]
]); ?>
