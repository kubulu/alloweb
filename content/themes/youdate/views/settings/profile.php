<?php

use app\helpers\Html;
use app\helpers\Timezone;
use app\models\Profile;
use app\models\Language;
use yii\helpers\ArrayHelper;
use youdate\widgets\ActiveForm;

/** @var $model \app\models\Profile */
/** @var $form \yii\widgets\ActiveForm */
/** @var $this \app\base\View */
/** @var $countries array */
/** @var $profileFields \app\models\ProfileField[] */
/** @var $profileFieldCategories \app\models\ProfileFieldCategory[] */
/** @var $profileExtra \app\models\ProfileExtra[] */
/** @var $extraModels \app\forms\ProfileExtraForm[] */

$this->title = Yii::t('youdate', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="card-body">
        <?= $this->render('/_alert') ?>

        <?php $form = ActiveForm::begin([
            'id' => 'profile-form',
            'options' => ['class' => 'form-horizontal'],
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'validateOnBlur' => false,
        ]); ?>

        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'description')->textarea() ?>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'timezone')
                    ->dropDownList(ArrayHelper::map(Timezone::getAll(), 'timezone', 'name'), [
                        'prompt' => Yii::t('youdate', 'Select timezone')
                    ]); ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'language_id')
                    ->dropDownList(ArrayHelper::map(Language::getLanguages(), 'language_id', 'name'), [
                        'prompt' => Yii::t('youdate', 'Select language')
                    ]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($model, 'dob')->textInput(['type' => 'date']) ?>
            </div>
            <div class="col-sm-4">
                <?= $form
                    ->field($model, 'country', ['inputOptions' => ['autocomplete' => 'off']])
                    ->widget(\youdate\widgets\CountrySelector::class, ['items' => array_merge([
                            null => Yii::t('youdate', 'Country'),
                    ], $countries)]) ?>
            </div>
            <div class="col-sm-4">
                <?= $form
                    ->field($model, 'city', ['inputOptions' => ['autocomplete' => 'off']])
                    ->widget(\youdate\widgets\CitySelector::class, [
                        'items' => [],
                        'preloadedValue' => ['value' => $model->city, 'title' => $model->getCityName()],
                    ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <div class="form-label"><?= $model->getAttributeLabel('status') ?></div>
                    <div class="custom-controls-stacked">
                        <?php foreach ($model->getStatusOptions() as $value => $title): ?>
                            <label class="custom-control custom-radio">
                                <?= Html::activeRadio($model, 'status', [
                                    'class' => 'custom-control-input',
                                    'value' => $value,
                                    'label' => false,
                                    'uncheck' => false,
                                ]) ?>
                                <span class="custom-control-label">
                                    <?= $title ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <div class="form-label"><?= $model->getAttributeLabel('sex') ?></div>
                    <div class="custom-controls-stacked">
                        <?php foreach ($model->getSexOptions() as $value => $title): ?>
                            <label class="custom-control custom-radio">
                                <?= Html::activeRadio($model, 'sex', [
                                    'class' => 'custom-control-input',
                                    'value' => $value,
                                    'label' => false,
                                    'uncheck' => false,
                                ]) ?>
                                <span class="custom-control-label">
                                    <?= $title ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <div class="form-label"><?= $model->getAttributeLabel('looking_for_sex') ?></div>
                    <div class="custom-controls-stacked">
                        <?php foreach ($model->getSexOptions() as $value => $title): ?>
                            <?php if ($value !== Profile::SEX_NOT_SET): ?>
                            <label class="custom-control custom-checkbox">
                                    <?= Html::activeCheckbox($model, 'looking_for_sex_array[' . $value . ']', [
                                        'class' => 'custom-control-input',
                                        'uncheck' => false,
                                        'value' => $value,
                                        'label' => false,
                                    ]) ?>
                                    <span class="custom-control-label">
                                        <?= $title ?>
                                    </span>
                                </label>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-6">
                        <?= $form->field($model, 'looking_for_from_age') ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'looking_for_to_age') ?>
                    </div>
                </div>
            </div>
        </div>

        <?= \yii\helpers\Html::submitButton(Yii::t('youdate', 'Save'), ['class' => 'btn float-right btn-primary']) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php foreach ($profileFieldCategories as $category): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <a name="<?= $category->alias ?>">
                <?= Html::encode(Yii::t($category->language_category, $category->title)) ?>
            </a>
        </h3>
    </div>
    <div class="card-body">
        <?php if (Yii::$app->session->hasFlash('success_' . $category->alias)): ?>
            <div class="alert alert-success">
                <?= Yii::$app->session->getFlash('success_' . $category->alias) ?>
            </div>
        <?php endif; ?>
        <?php $form = ActiveForm::begin([
            'id' => sprintf('profile-extra-%s-form', Html::encode($category->alias)),
            'options' => ['class' => 'form-horizontal'],
            'action' => ['extra-fields'],
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validateOnBlur' => false,
        ]); ?>
        <?= Html::hiddenInput('categoryAlias', $category->alias) ?>
        <?= Html::errorSummary($extraModels[$category->alias]) ?>
        <?php foreach (ArrayHelper::getValue($profileFields, $category->alias, []) as $field): ?>
            <?= $field->getFieldInstance()
                ->getFieldInput($form, $extraModels[$category->alias], [
                    'class' => 'form-control',
                ])->label(Yii::t($field->language_category, $field->title)) ?>
        <?php endforeach; ?>
        <?= Html::submitButton(Yii::t('youdate', 'Save'), ['class' => 'btn float-right btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php endforeach; ?>
