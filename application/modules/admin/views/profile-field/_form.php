<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\base\View;
use app\modules\admin\widgets\ProfileFieldForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProfileField */
/* @var $form yii\widgets\ActiveForm */
/* @var $categories \app\models\ProfileFieldCategory[] */
/* @var $fieldInstance \app\models\fields\BaseType */
/* @var $fieldClass string */
/* @var $fieldClasses array */
/* @var $formAction string */

$this->registerJs('
    $(".field-class-dropdown").on("change", function(event) {
        var className = $(this).find(":selected").val(),
            $form = $(".profile-field-category-form form"),
            url = $form.attr("action");
            
       $.pjax({
            url: url,
            container: "#pjax-profile-field-options",
            data: {
                "fieldClass": className
            },
            push: false,
            replace: false,
            timeout: 10000,
            "scrollTo" : false
        });
    });
', View::POS_READY);
?>

<div class="profile-field-category-form">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'action' => $formAction,
        'options' => [
            'base-url' => $model->isNewRecord ? Url::to(['create']) : Url::to(['update', 'id' => $model->id]),
        ],
    ]); ?>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <?= $form->field($model, 'category_id')
                ->dropDownList(ArrayHelper::map($categories, 'id', function(\app\models\ProfileFieldCategory $model) {
                    return Yii::t($model->language_category, $model->title);
                }), [
                    'prompt' => Yii::t('app', '-- Select --'),
                ])->label(Yii::t('app', 'Category')) ?>

            <?= $form->field($model, 'field_class')
                ->dropDownList($fieldClasses, [
                    'prompt' => Yii::t('app', '-- Select --'),
                    'class' => ['form-control field-class-dropdown'],
                ]) ?>

            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'alias')
                ->textInput(['maxlength' => true])
                ->hint(Yii::t('app', 'Alphanumeric symbols only'))?>

            <?= $form->field($model, 'language_category')
                ->textInput(['maxlength' => true])
                ->hint(Yii::t('app', 'Default is {0}', '<code>app</code>'))?>

            <?= $form->field($model, 'sort_order')
                ->textInput()
                ->hint(Yii::t('app', 'Default is {0}', 100))?>

            <?= $form->field($model, 'is_visible')->checkbox() ?>
        </div>
        <div class="col-xs-12 col-sm-6">
            <?php \yii\widgets\Pjax::begin(['id' => 'pjax-profile-field-options']) ?>
                <?php if ($fieldInstance !== null): ?>
                    <?= ProfileFieldForm::widget([
                        'fieldInstance' => $fieldInstance,
                        'form' => $form,
                        'model' => $model,
                    ]) ?>
                <?php endif; ?>
            <?php yii\widgets\Pjax::end() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
