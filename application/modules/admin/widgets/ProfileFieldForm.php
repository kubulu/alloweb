<?php

namespace app\modules\admin\widgets;

use app\models\fields\BaseType;
use Yii;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\widgets
 */
class ProfileFieldForm extends Widget
{
    /**
     * @var BaseType
     */
    public $fieldInstance;
    /**
     * @var ActiveForm
     */
    public $form;
    /**
     * @var ActiveRecord
     */
    public $model;

    /**
     * @return string
     */
    public function run()
    {
        $output = '';
        foreach ($this->fieldInstance->getFieldOptions() as $attribute => $options) {
            $field = $this->form->field($this->model, $attribute);
            $type = ArrayHelper::remove($options, 'type', 'text');
            $label = ArrayHelper::remove($options, 'label');
            $hint = ArrayHelper::remove($options, 'hint', false);
            $items = ArrayHelper::remove($options, 'items', []);
            switch ($type) {
                case 'text':
                    $field->textInput($options)->label($label)->hint($hint);
                    break;
                case 'textarea':
                    $field->textarea($options)->label($label)->hint($hint);
                    break;
                case 'checkbox':
                    $field->checkbox($options)->hint($hint);
                    break;
                case 'dropdown':
                    if (!isset($options['prompt'])) {
                        $options['prompt'] = Yii::t('app', '-- Select --');
                    }
                    $field->dropDownList($items, $options);
                    break;
            }
            $output .= $field . "\n";
        }

        return $output;
    }
}
