<?php

namespace app\modules\admin\components\translations;

use Yii;
use yii\helpers\Json;
use yii\base\InvalidConfigException;
use app\modules\admin\components\Translations;
use app\models\LanguageSource;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\components\translations
 */
class Generator
{
    /**
     * Location of generated language files.
     */
    private $_basePath;

    /**
     * @var string Language of current translation. The JavaScript language file will be generated in this language.
     */
    private $_languageId;

    /**
     * @var array The array containing the language elements. [md5('Framework') => 'Framework']
     */
    private $_languageItems = [];

    /**
     * @var string JavaScript code for serving language elements.
     */
    private $_template = 'var languageItems=(function(){var _languages={language_items};return{getLanguageItems:function(){return _languages;}};})();';

    /**
     * @param $module
     * @param $language_id
     * @throws InvalidConfigException
     */
    public function __construct($module, $language_id)
    {
        /** @var Translations $component */
        $component = $module->get('translations');
        $this->_languageId = $language_id;
        $this->_basePath = Yii::getAlias($component->tmpDir);
        if (!is_dir($this->_basePath)) {
            throw new InvalidConfigException("The directory does not exist: {$this->_basePath}");
        } elseif (!is_writable($this->_basePath)) {
            throw new InvalidConfigException("The directory is not writable by the Web process: {$this->_basePath}");
        }

        $this->_basePath = $component->getLanguageItemsDirPath();
        if (!is_dir($this->_basePath)) {
            mkdir($this->_basePath);
        }

        if (!is_writable($this->_basePath)) {
            throw new InvalidConfigException("The directory is not writable by the Web process: {$this->_basePath}");
        }
    }

    /**
     * @return int
     */
    public function generate()
    {
        return $this->run();
    }

    /**
     * @return int
     */
    public function run()
    {
        $this->_generateJSFile();

        return count($this->_languageItems);
    }

    private function _generateJSFile()
    {
        $this->_loadLanguageItems();

        $data = [];
        foreach ($this->_languageItems as $language_item) {
            $data[md5($language_item->message)] = $language_item->languageTranslate->translation;
        }

        $filename = $this->_basePath . '/' . $this->_languageId . '.js';

        file_put_contents($filename, str_replace('{language_items}', Json::encode($data), $this->_template));
    }

    private function _loadLanguageItems()
    {
        $this->_languageItems = LanguageSource::find()
            ->joinWith(['languageTranslate' => function ($query) {
                $query->where(['language' => $this->_languageId]);
            },
            ])
            ->where(['category' => Scanner::CATEGORY_JAVASCRIPT])
            ->all();
    }

    /**
     * @return string returns the language id of the translation.
     */
    public function getLanguageId()
    {
        return $this->_languageId;
    }

    /**
     * @param string $language_id Stores the language id of the translation.
     */
    public function setLanguageId($language_id)
    {
        $this->_languageId = $language_id;
    }
}
