<?php

namespace app\modules\admin\components\translations;

use app\modules\admin\components\Translations;
use app\modules\admin\traits\TranslationsComponentTrait;
use app\models\LanguageSource;
use Yii;
use yii\helpers\Console;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\components\translations
 */
class Scanner
{
    use TranslationsComponentTrait;

    /**
     * JavaScript category.
     */
    const CATEGORY_JAVASCRIPT = 'javascript';
    /**
     * Array category.
     */
    const CATEGORY_ARRAY = 'array';
    /**
     * Database category.
     */
    const CATEGORY_DATABASE = 'database';
    /**
     * @var int
     */
    public $scanTimeLimit = 300;
    /**
     * @var array
     */
    public $scanners = [
        \app\modules\admin\components\translations\scanners\ScannerPhpFunction::class,
        \app\modules\admin\components\translations\scanners\ScannerDatabase::class,
        \app\modules\admin\components\translations\scanners\ScannerProfileFields::class,
    ];
    /**
     * @var array
     */
    private $_languageElements = [];
    /**
     * @var array
     */
    private $_removableLanguageSourceIds = [];

    /**
     * @return int
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function run()
    {
        set_time_limit($this->scanTimeLimit);

        $scanners = $this->getTranslations()->scanners;
        if (!empty($scanners)) {
            $this->scanners = $scanners; // override scanners from module configuration (custom scanners)
        }

        $this->_initLanguageArrays();

        $languageSource = new LanguageSource();

        return $languageSource->insertLanguageItems($this->_languageElements);
    }

    /**
     * @return array
     */
    public function getNewLanguageElements()
    {
        return $this->_languageElements;
    }

    /**
     * @return array
     */
    public function getRemovableLanguageSourceIds()
    {
        return $this->_removableLanguageSourceIds;
    }

    private function _initLanguageArrays()
    {
        $this->_scanningProject();

        $languageSources = LanguageSource::find()->all();

        foreach ($languageSources as $languageSource) {
            if (isset($this->_languageElements[$languageSource->category][$languageSource->message])) {
                unset($this->_languageElements[$languageSource->category][$languageSource->message]);
            } else {
                $this->_removableLanguageSourceIds[$languageSource->id] = $languageSource->id;
            }
        }
    }


    private function _scanningProject()
    {
        foreach ($this->scanners as $scanner) {
            $object = new $scanner($this);
            $object->run('');
        }
    }

    /**
     * Adding language elements to the array.
     *
     * @param string $category
     * @param string $message
     */
    public function addLanguageItem($category, $message)
    {
        $this->_languageElements[$category][$message] = true;

        $coloredCategory = Console::ansiFormat($category, [Console::FG_YELLOW]);
        $coloredMessage = Console::ansiFormat($message, [Console::FG_YELLOW]);

        $this->stdout('Detected language element: [ ' . $coloredCategory . ' ] ' . $coloredMessage);
    }

    /**
     * @param $languageItems
     */
    public function addLanguageItems($languageItems)
    {
        foreach ($languageItems as $languageItem) {
            $this->addLanguageItem($languageItem['category'], $languageItem['message']);
        }
    }

    /**
     * @param $string
     */
    public function stdout($string)
    {
        if (Yii::$app->request->isConsoleRequest) {
            if (Console::streamSupportsAnsiColors(STDOUT)) {
                $args = func_get_args();
                array_shift($args);
                $string = Console::ansiFormat($string, $args);
            }

            Console::stdout($string . "\n");
        }
    }
}
