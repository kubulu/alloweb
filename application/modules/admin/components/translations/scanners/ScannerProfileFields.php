<?php

namespace app\modules\admin\components\translations\scanners;

use app\models\ProfileField;
use app\modules\admin\traits\TranslationsComponentTrait;
use app\modules\admin\components\translations\Scanner;
use yii\helpers\Console;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\components\translations\scanners
 */
class ScannerProfileFields
{
    use TranslationsComponentTrait;

    /**
     * @var Scanner object containing the detected language elements
     */
    private $_scanner;

    public function __construct(Scanner $scanner)
    {
        $this->_scanner = $scanner;
    }

    public function run()
    {
        $this->_scanner->stdout('Detect ProfileFields - BEGIN', Console::FG_GREY);

        /** @var ProfileField[] $fields */
        $fields = ProfileField::find()->where(['field_class' => 'app\models\fields\Select'])->all();
        foreach ($fields as $field) {
            $config = json_decode($field->field_config, true);
            if (!isset($config['options']) || empty($config['options'])) {
                continue;
            }
            $values = explode("\n", $config['options']);
            foreach ($values as $value) {
                try {
                    $labels = explode('=>', $value);
                    if (count($labels) == 2) {
                        $label = trim($labels[1]);
                    } else {
                        $label = trim($labels[0]);
                    }
                    if (mb_strlen($label)) {
                        $this->_scanner->addLanguageItem($field->language_category, trim($label));
                    }
                } catch (\Exception $e) {
                    Yii::warning($e->getMessage());
                }
            }
        }

        $this->_scanner->stdout('Detect ProfileFields - END', Console::FG_GREY);
    }
}
