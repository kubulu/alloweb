<?php

use app\helpers\Html;
use app\models\Profile;
use app\models\Sex;
use youdate\helpers\HtmlHelper;

/** @var \yii\web\View $this **/
/** @var \app\forms\RegistrationForm $model **/
/** @var \app\models\Account $account */
/** @var array $sexOptions **/
/** @var array $countries **/
?>
<div class="row">
    <div class="col-6">
        <div class="selectgroup selectgroup-pills">
            <?php foreach ((new Profile())->getSexModels() as $sexModel): ?>
                <label class="selectgroup-item">
                    <input type="radio" name="<?= Html::getInputName($model, 'sex') ?>"
                           value="<?= $sexModel->sex ?? \app\models\Profile::SEX_NOT_SET ?>" class="selectgroup-input">
                    <span class="selectgroup-button selectgroup-button-icon"
                          rel="tooltip"
                          title="<?= Html::encode($sexModel instanceof Sex ? $sexModel->getTitle() : $sexModel) ?>">
                      <?= HtmlHelper::sexToIcon($sexModel) ?>
                </span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-6">
        <?= $form->field($model, 'dob')->textInput(['type' => 'date']) ?>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <?= $form->field($model, 'name') ?>
    </div>
    <div class="col-6">
        <?= $form->field($model, 'username') ?>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <?= $form
            ->field($model, 'country', ['inputOptions' => ['autocomplete' => 'off']])
            ->widget(\youdate\widgets\CountrySelector::class, ['items' => array_merge([
                null => Yii::t('youdate', 'Country'),
            ], $countries)]) ?>
    </div>
    <div class="col-6">
        <?= $form
            ->field($model, 'city', ['inputOptions' => ['autocomplete' => 'off']])
            ->widget(\youdate\widgets\CitySelector::class, [
                'items' => [],
            ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <?= $form->field($model, 'email') ?>
    </div>
    <div class="col-6">
        <?= $form->field($model, 'password')->passwordInput() ?>
    </div>
</div>

<?php if ($model->isCaptchaRequired()): ?>
    <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::class) ?>
<?php endif; ?>

<div class="terms text-muted pb-3 text-center">
    <?= Yii::t('youdate', 'By continuing, you\'re confirming that you\'ve read and agree to our {terms}, {privacy} and {cookie}', [
        'terms' => Html::a(Yii::t('youdate', 'Terms and Conditions'), ['/page/terms-and-conditions']),
        'privacy' => Html::a(Yii::t('youdate', 'Privacy Policy'), ['/page/privacy-policy']),
        'cookie' => Html::a(Yii::t('youdate', 'Cookie Policy'), ['/page/cookie-policy']),
    ]) ?>
</div>
