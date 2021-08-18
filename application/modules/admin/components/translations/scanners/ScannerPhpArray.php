<?php

namespace app\modules\admin\components\translations\scanners;

use yii\helpers\Console;
use app\modules\admin\components\translations\Scanner;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\components\translations\scanners
 */
class ScannerPhpArray extends ScannerFile
{
    /**
     * Extension of PHP files.
     */
    const EXTENSION = '*.php';

    /**
     * @param string $route
     * @param array $params
     * @return mixed|void
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function run($route, $params = [])
    {
        $this->scanner->stdout('Detect PhpArray - BEGIN', Console::FG_BLUE);
        foreach (self::$files[static::EXTENSION] as $file) {
            foreach ($this->_getTranslators($file) as $translator) {
                $this->extractMessages($file, [
                    'translator' => [$translator],
                    'begin' => (preg_match('#array\s*$#i', $translator) != false) ? '(' : '[',
                    'end' => ';',
                ]);
            }
        }

        $this->scanner->stdout('Detect PhpArray - END', Console::FG_BLUE);
    }

    /**
     * @param $file
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function _getTranslators($file)
    {
        $subject = file_get_contents($file);
        preg_match_all($this->getTranslations()->patternArrayTranslator, $subject, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        $translators = [];
        foreach ($matches as $data) {
            if (isset($data['translator'][0])) {
                $translators[$data['translator'][0]] = true;
            }
        }

        return array_keys($translators);
    }

    /**
     * @param $buffer
     * @return mixed|null
     */
    protected function getLanguageItem($buffer)
    {
        $index = -1;
        $languageItems = [];
        foreach ($buffer as $key => $data) {
            if (isset($data[0], $data[1]) && $data[0] === T_CONSTANT_ENCAPSED_STRING) {
                $message = stripcslashes($data[1]);
                $message = mb_substr($message, 1, mb_strlen($message) - 2);
                if (isset($buffer[$key - 1][0]) && $buffer[$key - 1][0] === '.') {
                    $languageItems[$index]['message'] .= $message;
                } else {
                    $languageItems[++$index] = [
                        'category' => Scanner::CATEGORY_ARRAY,
                        'message' => $message,
                    ];
                }
            }
        }

        return $languageItems ?: null;
    }
}
