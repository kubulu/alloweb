<?php

namespace app\modules\admin\components\translations\scanners;

use app\modules\admin\traits\TranslationsComponentTrait;
use yii\helpers\Console;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\components\translations\services\scanners
 */
class ScannerPhpFunction extends ScannerFile
{
    use TranslationsComponentTrait;

    /**
     * Extension of PHP files.
     */
    const EXTENSION = '*.php';

    /**
     * Start scanning PHP files.
     *
     * @param string $route
     * @param array $params
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     * @inheritdoc
     */
    public function run($route, $params = [])
    {
        $this->scanner->stdout('Detect PhpFunction - BEGIN', Console::FG_CYAN);
        foreach (self::$files[static::EXTENSION] as $file) {
            if ($this->containsTranslator($this->getTranslations()->phpTranslators, $file)) {
                $this->extractMessages($file, [
                    'translator' => (array)$this->getTranslations()->phpTranslators,
                    'begin' => '(',
                    'end' => ')',
                ]);
            }
        }

        $this->scanner->stdout('Detect PhpFunction - END', Console::FG_CYAN);
    }

    /**
     * @param $buffer
     * @return array|mixed|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function getLanguageItem($buffer)
    {
        if (isset($buffer[0][0], $buffer[1], $buffer[2][0]) && $buffer[0][0] === T_CONSTANT_ENCAPSED_STRING && $buffer[1] === ',' && $buffer[2][0] === T_CONSTANT_ENCAPSED_STRING) {
            // is valid call we can extract
            $category = stripcslashes($buffer[0][1]);
            $category = mb_substr($category, 1, mb_strlen($category) - 2);
            if (!$this->isValidCategory($category)) {
                return null;
            }

            $message = implode('', $this->concatMessage($buffer));

            return [
                [
                    'category' => $category,
                    'message' => $message,
                ],
            ];
        }

        return null;
    }

    /**
     * @param $buffer
     * @return array
     */
    protected function concatMessage($buffer)
    {
        $messages = [];
        $buffer = array_slice($buffer, 2);
        $message = stripcslashes($buffer[0][1]);
        $messages[] = mb_substr($message, 1, mb_strlen($message) - 2);
        if (isset($buffer[1], $buffer[2][0]) && $buffer[1] === '.' && $buffer[2][0] == T_CONSTANT_ENCAPSED_STRING) {
            $messages = array_merge_recursive($messages, $this->concatMessage($buffer));
        }

        return $messages;
    }
}
