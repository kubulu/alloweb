<?php

namespace youdate\widgets;
use app\helpers\Html;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class ActiveForm extends \yii\widgets\ActiveForm
{
    /**
     * @var string
     */
    public $fieldClass = \youdate\widgets\ActiveField::class;
    /**
     * @var string
     */
    public $errorSummaryCssClass = 'error-summary alert alert-danger';

    /**
     * @param \yii\base\Model|\yii\base\Model[] $models
     * @param array $options
     * @return string
     */
    public function errorSummary($models, $options = [])
    {
        Html::addCssClass($options, $this->errorSummaryCssClass);
        $options['encode'] = $this->encodeErrorSummary;
        $options['header'] = false;
        $options['footer'] = false;

        return Html::errorSummary($models, $options);
    }
}
