<?php

use youdate\widgets\DirectorySearchForm;
use youdate\widgets\DirectoryListView;
use youdate\widgets\EmptyState;

/* @var $this \app\base\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $searchForm \app\forms\UserSearchForm */
/* @var $countries array */
/* @var $currentCity array */

$this->title = Yii::t('youdate', 'Find People');
$this->context->layout = 'page-main';
$this->params['body.cssClass'] = 'body-directory-index';
$premiumFeaturesEnabled = \yii\helpers\ArrayHelper::getValue($this->params, 'site.premiumFeatures.enabled');
$this->registerJs('
    var pageLoaded = false;
    $("#search-form input, #search-form select").on("change", function(event) {
        var $form = $("#search-form");
        if (!pageLoaded) {
            pageLoaded = true;
            return;
        }
        $.pjax({
            url: $form.attr("action") + "?" + $form.serialize(),
            container: "#pjax-directory-list-view",
            push: false,
            replace: true,
            timeout: 10000,
            "scrollTo": false
        });
    });
', \app\base\View::POS_READY);
?>
<?= DirectorySearchForm::widget([
    'user' => $user,
    'model' => $searchForm,
    'countries' => $countries,
    'currentCity' => $currentCity,
]) ?>
<?php \yii\widgets\Pjax::begin(['id' => 'pjax-directory-list-view', 'options' => ['data-pjax-scroll-to' => 'body']]) ?>
<?php if ($dataProvider->getTotalCount()): ?>
    <?= DirectoryListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => function($model, $key, $index, $widget) use ($premiumFeaturesEnabled) {
            $html = '';
            if ($index == 8 && $premiumFeaturesEnabled) {
                $html = $this->render('_boost');
            }
            $html .= $this->render('_item', ['model' => $model]);
            return $html;
        },
        'itemOptions' => ['tag' => false],
    ]) ?>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <?= EmptyState::widget([
                'icon' => 'fe fe-users',
                'title' => Yii::t('youdate', 'Users not found'),
                'subTitle' => Yii::t('youdate', 'You can try to narrow your search filters'),
            ]) ?>
        </div>
    </div>
<?php endif; ?>
<?php \yii\widgets\Pjax::end() ?>

