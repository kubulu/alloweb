<?php

use app\helpers\Html;
use app\models\Sex;
use app\forms\UserSearchForm;
use youdate\helpers\HtmlHelper;
use youdate\widgets\ActiveForm;

/** @var $model \app\forms\UserSearchForm */
/** @var $this \app\base\View */
/** @var $countries array */
/** @var $currentCity array */
/** @var $showMoreCookie string */
/** @var $showMoreVisible bool */

$locationAddressCss = $model->locationType == UserSearchForm::LOCATION_TYPE_NEAR ? 'hidden' : '';
$locationNearCss = $model->locationType == UserSearchForm::LOCATION_TYPE_ADDRESS ? 'hidden' : '';

$this->registerJs('
    $("body").on("change", "input[name=locationType]", function(event) {
        var $selected = $("input[name=locationType]:checked");
        $(".location-type").addClass("hidden");
        $(".location-type.location-" + $selected.val()).removeClass("hidden");
    });
', \app\base\View::POS_READY);
?>

<?php $form = ActiveForm::begin([
    'id' => 'search-form',
    'method' => 'get',
    'action' => ['/directory/index'],
    'options' => ['class' => 'form-horizontal'],
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    'validateOnBlur' => false,
]); ?>

<div class="card directory-search-form">
    <div class="card-body pt-3 pb-1">
        <div class="row">
            <?= Html::errorSummary($model, [
                'header' => false,
                'class' => 'alert bg-red text-white w-100',
            ]) ?>
        </div>
        <div class="row">
            <div class="col-sex col-sm-6 col-md-6 col-lg-2">
                <div class="form-group">
                    <div class="form-label"><?= Yii::t('youdate', 'I\'m looking for') ?></div>
                    <div class="selectgroup selectgroup-pills">
                        <?php foreach ($model->getSexModels() as $sexModel): ?>
                            <label class="selectgroup-item">
                                <input type="radio" name="<?= Html::getInputName($model, 'sex') ?>"
                                       value="<?= $sexModel->sex ?? \app\models\Profile::SEX_NOT_SET ?>" class="selectgroup-input">
                                <span class="selectgroup-button selectgroup-button-icon"
                                      rel="tooltip"
                                      title="<?= Html::encode($sexModel instanceof Sex ? $sexModel->getTitle(true) : $sexModel) ?>">
                                    <?= HtmlHelper::sexToIcon($sexModel, true) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-age col-sm-6 col-md-6 col-lg-2">
                <div class="form-group">
                    <div class="form-label"><?= Yii::t('youdate', 'Aged') ?></div>
                    <div class="row">
                        <div class="col-6">
                            <?= Html::activeTextInput($model, 'fromAge', [
                                'class' => 'form-control',
                                'type' => 'number',
                                'min' => 18,
                                'step' => 1,
                            ]) ?>
                        </div>
                        <div class="col-6">
                            <?= Html::activeTextInput($model, 'toAge', [
                                'class' => 'form-control',
                                'type' => 'number',
                                'min' => 18,
                                'max' => 100,
                                'step' => 1,
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-location col-sm-12 col-md-12 col-lg-6">
                <div class="custom-controls-stacked">
                    <div class="form-label"><?= Yii::t('youdate', 'Located') ?></div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 ">
                            <?php foreach ($model->getLocationTypeOptions() as $value => $label): ?>
                                <label class="custom-control custom-radio" style="margin-bottom: 0">
                                    <?= Html::radio(Html::getInputName($model, 'locationType'),
                                        $value == $model->locationType, [
                                            'class' => 'custom-control-input',
                                            'value' => $value,
                                        ]); ?>
                                    <div class="custom-control-label"><?= $label ?></div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mt-2 mt-lg-0 location-type location-address <?= $locationAddressCss ?>">
                            <?= \youdate\widgets\CountrySelector::widget([
                                'items' => $countries,
                                'model' => $model,
                                'attribute' => 'country',
                            ]) ?>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mt-2 mt-lg-0 location-type location-address <?= $locationAddressCss ?>">
                            <?= \youdate\widgets\CitySelector::widget([
                                'items' => [],
                                'model' => $model,
                                'attribute' => 'city',
                                'preloadedValue' => ['value' => $currentCity['value'], 'title' => $currentCity['title']],
                            ]) ?>
                        </div>
                        <div class="col-12 col-sm-8 col-lg-8 mt-2 mt-lg-0 location-type location-near <?= $locationNearCss ?>">
                            <?= $form->field($model, 'distance', ['options' => ['class' => '']])
                                ->radioList($model->getDistanceOptions(), [
                                    'class' => 'selectgroup selectgroup-pills',
                                    'unselect' => null,
                                    'item' => function($index, $label, $name, $checked, $value) {
                                        $return = '<label class="selectgroup-item">';
                                        $return .= '<input type="radio" class="selectgroup-input" 
                                        name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>';
                                        $return .= ' <span class="selectgroup-button selectgroup-button-icon" rel="tooltip" 
                                        title="' . Html::encode($label) . '">' . HtmlHelper::distanceToLabel($value) .'</span>';
                                        $return .= '</label>';
                                        return $return;
                                    }])
                                ->label(false) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-search-actions col-12 col-sm-12 col-md-12 col-lg-2">
                <div class="form-group">
                    <div class="form-label">&nbsp;</div>
                    <div class="btn-group w-100">
                        <?= \yii\helpers\Html::submitButton('<i class="fe fe-search pr-2"></i>' . Yii::t('youdate', 'Search'), [
                            'class' => 'btn btn-primary btn-block'
                        ]) ?>
                        <button class="btn btn-primary" type="button"
                                data-toggle-visibility-cookie="<?= $showMoreCookie ?>"
                                data-toggle-visibility-target=".directory-search-more">
                            <i class="fa fa-cog"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="directory-search-more"
             class="directory-search-more d-flex flex-column flex-sm-row justify-content-start align-items-start align-items-sm-center <?= $showMoreVisible ? '' : 'hidden' ?>">
            <?= $form->field($model, 'online')->checkbox(['uncheck' => false]) ?>
            <?= $form->field($model, 'verified')->checkbox(['uncheck' => false]) ?>
            <?= $form->field($model, 'withPhoto')->checkbox() ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
