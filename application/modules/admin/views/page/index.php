<?php

use app\helpers\Url;
use app\helpers\Html;

/** @var $this \yii\web\View */
/** @var $pages array */
/** @var $currentPage string */
/** @var $content string */
/** @var $pagesEditable bool */

$this->title = Yii::t('app', 'Manage pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12 col-md-3">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', 'Pages') ?></h3>
            </div>
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                    <?php foreach ($pages as $page): ?>
                        <li class="<?= $currentPage == basename($page) ? 'active' : '' ?> ">
                            <a href="<?= Url::to(['index', 'currentPage' => basename($page)]) ?>">
                                <i class="fa fa-edit"></i> <?= basename($page) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-9">
        <?php $form = \yii\widgets\ActiveForm::begin([
            'action' => ['save', 'currentPage' => $currentPage],
            'method' => 'post'
        ]) ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', 'Editor') ?></h3>
                <div class="box-tools pull-right">
                    <?= Html::submitButton(Yii::t('app', 'Save'), [
                        'class' => 'btn btn-primary btn-sm',
                    ]) ?>
                    <?= Html::a(Yii::t('app', 'Reset pages'), ['reset'], [
                        'class' => 'btn btn-danger btn-sm',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('app', 'Do you really want to restore pages from theme files?'),
                    ]) ?>
                </div>
            </div>
            <div class="box-body" style="min-height: 200px;">
                <?php if (!$pagesEditable): ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> <?= Yii::t('app', 'Page editing is disabled by app config') ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-info-circle"></i>  <?= Yii::t('app', 'Warning. Edit these files carefully') ?>
                    </div>
                <?php endif; ?>
                <?php if ($currentPage !== null): ?>
                    <?= \trntv\aceeditor\AceEditor::widget([
                        'name' => 'content',
                        'mode' => 'php',
                        'value' => $content,
                        'options' => ['id' => 'editor'],
                    ]) ?>
                <?php else: ?>
                    <p><?= Yii::t('app', 'Choose file to edit') ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php \yii\widgets\ActiveForm::end() ?>
    </div>
</div>
