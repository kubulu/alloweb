<?php

use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;

/* @var $this \yii\web\View */
/* @var $currentThemeId string */
/* @var $currentThemeInfo array */
/* @var $themes array */

$this->title = 'Themes catalog';
$this->params['breadcrumbs'][] = ['label' => 'Themes', 'url' => ['index']];
?>
<div class="row">
    <?php foreach ($themes as $themeId => $theme): ?>
        <?php $active = $themeId == $currentThemeId; ?>
        <div class="col-md-4">
            <div class="box box-<?= $active ? 'info' : 'default' ?>">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Html::encode($theme['title']) ?></h3>
                    <?php if ($active): ?>
                        <span class="label label-info pull-right">active</span>
                    <?php endif; ?>
                </div>
                <div class="box-body no-padding">
                    <div class="theme-screenshot">
                        <?php if ($theme['screenshot']): ?>
                            <?= Html::img($theme['screenshot'], ['class' => 'img img-responsive', 'alt' => $theme['title']]) ?>
                        <?php else: ?>
                            <div class="no-screenshot"></div>
                        <?php endif; ?>
                        <div class="theme-version">
                            <span class="label label-default"><?= Html::encode($theme['version']) ?></span>
                        </div>
                    </div>
                    <div class="theme-info">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-sm-push-6">
                                <?php if ($active): ?>
                                    <?= Html::a(FA::icon('cog') . 'Settings', ['settings'], [
                                        'class' => 'btn btn-primary btn-block',
                                    ]) ?>
                                <?php else: ?>
                                    <?= Html::a(FA::icon('download') . 'Activate', ['activate', 'themeId' => $themeId], [
                                        'class' => 'btn btn-default btn-block',
                                        'data-method' => 'post'
                                    ]) ?>
                                <?php endif ?>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-sm-pull-6">
                                <?php if ($theme['author']): ?>
                                    <div class="theme-author"><strong><?= Html::encode($theme['author']) ?></strong></div>
                                <?php endif; ?>
                                <?php if ($theme['website']): ?>
                                    <div class="theme-website"><strong><?= Html::a($theme['website'], $theme['website'], ['rel' => 'nofollow']) ?></strong></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
