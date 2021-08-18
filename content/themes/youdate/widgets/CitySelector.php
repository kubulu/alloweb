<?php

namespace youdate\widgets;

use app\helpers\Url;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class CitySelector extends SelectizeDropDownList
{
    /**
     * @var array
     */
    public $options = ['class' => 'city-selector', 'style' => 'height: 38px'];
    /**
     * @var array 'value' and 'title'
     */
    public $preloadedValue;

    public function init()
    {
        parent::init();
        $this->clientOptions = [
            'render' => new \yii\web\JsExpression('function(item, escape) {
                return item.name;
            }'),
            'load' => new \yii\web\JsExpression('function(query, callback) {
                if (!query.length) return callback();
                    $.ajax({
                        url: \'' . Url::to(['/site/find-cities']) . '\',
                        type: \'GET\',
                        dataType: \'json\',
                        data: {
                            country: $(\'.country-selector\').val(),
                            query: query,
                        },
                        error: function() {
                            callback();
                        },
                        success: function(response) {
                            callback(response.cities);
                        }
                });
            }'),
            'onInitialize' => new \yii\web\JsExpression('function(){
                var selectize = this;
                var preloadedValue = ' . (int) $this->preloadedValue['value'] . ';
                if (preloadedValue) {
                    selectize.addOption({\'value\': preloadedValue, \'text\': \''. $this->preloadedValue['title'] . '\'});
                    selectize.setValue(preloadedValue);
                }
            }')
        ];
    }
}
