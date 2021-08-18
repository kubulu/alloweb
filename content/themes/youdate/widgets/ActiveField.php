<?php

namespace youdate\widgets;

use app\helpers\Html;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class ActiveField extends \yii\widgets\ActiveField
{
    /**
     * @var array
     */
    public $labelOptions = ['class' => 'form-label'];

    /**
     * @param array $options
     * @param bool $enclosedByLabel
     * @return \yii\widgets\ActiveField|ActiveField
     */
    public function checkbox($options = [], $enclosedByLabel = false)
    {
        $options['class'] = 'custom-control-input';

        if (!isset($options['uncheck'])) {
            $this->parts['{uncheck}'] = Html::hiddenInput(Html::getInputName($this->model,$this->attribute), 0);
            $options['uncheck'] = false;
        } else {
            $this->parts['{uncheck}'] = '';
        }

        $this->template = '
            {uncheck}
            <label class="custom-control custom-checkbox">
                {input}
                <span class="custom-control-label">{labelText}</span>
            </label>';

        $this->parts['{name}'] = $this->attribute;
        $this->parts['{value}'] = $this->model->{$this->attribute};
        $this->parts['{labelText}'] = $this->model->getAttributeLabel($this->attribute);

        return parent::checkbox($options, $enclosedByLabel);
    }

    public function radio($options = [], $enclosedByLabel = true)
    {
        $this->labelOptions['class'] = 'custom-control custom-radio';
        $this->parts['{customInput}'] = Html::activeRadio($this->model, $this->attribute, [
            'class' => 'custom-control-input',
            'label' => false,
        ]);
        $this->radioTemplate = "<label class=\"custom-control custom-radio\">{customInput}<div class=\"custom-control-label\">{labelTitle}\n</div></label>\n{error}\n{hint}";

        return parent::radio($options, $enclosedByLabel);
    }
}
