<?php

namespace app\modules\admin\components\translations\scanners;

use app\modules\admin\components\Translations;
use app\modules\admin\traits\TranslationsComponentTrait;
use Yii;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\base\InvalidConfigException;
use app\modules\admin\components\translations\Scanner;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\components\translations\scanners
 */
abstract class ScannerFile extends \yii\console\controllers\MessageController
{
    use TranslationsComponentTrait;

    const EXTENSION = '*.php';

    /**
     * @var Scanner object.
     */
    public $scanner;
    /**
     * @var array
     */
    protected static $files = ['*.php' => [], '*.js' => []];

    /**
     * @param Scanner $scanner
     */
    public function __construct(Scanner $scanner)
    {
        parent::__construct('language', Yii::$app->module, [
            'scanner' => $scanner,
        ]);
    }

    /**
     * @inheritdoc Initialise the $files static array.
     */
    public function init()
    {
        $this->initFiles();

        parent::init();
    }

    protected function initFiles()
    {
        if (!empty(self::$files[static::EXTENSION]) || !in_array(static::EXTENSION, $this->getTranslations()->patterns)) {
            return;
        }

        self::$files[static::EXTENSION] = [];

        foreach ($this->_getRoots() as $root) {
            $root = realpath($root);
            Yii::info('Scanning ' . static::EXTENSION . " files for language elements in: $root", 'translatemanager');

            $files = FileHelper::findFiles($root, [
                'except' => $this->getTranslations()->ignoredItems,
                'only' => [static::EXTENSION],
            ]);
            self::$files[static::EXTENSION] = array_merge(self::$files[static::EXTENSION], $files);
        }

        self::$files[static::EXTENSION] = array_unique(self::$files[static::EXTENSION]);
    }

    /**
     * @param $translators
     * @param $file
     * @return bool
     */
    protected function containsTranslator($translators, $file)
    {
        return preg_match(
            '#(' . implode('\s*\()|(', array_map('preg_quote', $translators)) . '\s*\()#i',
            file_get_contents($file)
        ) > 0;
    }

    /**
     * @param string $fileName
     * @param string|array $options
     * @param array $ignoreCategories
     * @return array|void
     */
    protected function extractMessages($fileName, $options, $ignoreCategories = [])
    {
        $this->scanner->stdout('Extracting messages from ' . $fileName, Console::FG_GREEN);
        $subject = file_get_contents($fileName);
        if (static::EXTENSION !== '*.php') {
            $subject = "<?php\n" . $subject;
        }

        foreach ($options['translator'] as $currentTranslator) {
            $translatorTokens = token_get_all('<?php ' . $currentTranslator);
            array_shift($translatorTokens);

            $tokens = token_get_all($subject);

            $this->checkTokens($options, $translatorTokens, $tokens);
        }
    }

    /**
     * @param array $options Definition of the parameters required to identify language elements.
     * @param array $translatorTokens Translation identification
     * @param array $tokens Tokens to search through
     */
    protected function checkTokens($options, $translatorTokens, $tokens)
    {
        $translatorTokensCount = count($translatorTokens);
        $matchedTokensCount = 0;
        $buffer = [];

        foreach ($tokens as $token) {
            // finding out translator call
            if ($matchedTokensCount < $translatorTokensCount) {
                if ($this->tokensEqual($token, $translatorTokens[$matchedTokensCount])) {
                    ++$matchedTokensCount;
                } else {
                    $matchedTokensCount = 0;
                }
            } elseif ($matchedTokensCount === $translatorTokensCount) {
                // translator found
                // end of translator call or end of something that we can't extract
                if ($this->tokensEqual($options['end'], $token)) {
                    $languageItems = $this->getLanguageItem($buffer);
                    if ($languageItems) {
                        $this->scanner->addLanguageItems($languageItems);
                    }

                    if (count($buffer) > 4 && $buffer[3] == ',') {
                        array_splice($buffer, 0, 4);
                        $buffer[] = $options['end']; //append an end marker stripped by the current check
                        $this->checkTokens($options, $translatorTokens, $buffer);
                    }

                    // prepare for the next match
                    $matchedTokensCount = 0;
                    $buffer = [];
                } elseif ($token !== $options['begin'] && isset($token[0]) && !in_array($token[0],
                        [T_WHITESPACE, T_COMMENT])
                ) {
                    // ignore comments, whitespaces and beginning of function call
                    $buffer[] = $token;
                }
            }
        }
    }

    /**
     * @param $buffer
     * @return mixed
     */
    abstract protected function getLanguageItem($buffer);

    /**
     * @return array
     * @throws InvalidConfigException
     */
    private function _getRoots()
    {
        $directories = [];

        if (is_string($this->getTranslations()->root)) {
            $root = Yii::getAlias($this->getTranslations()->root);
            if ($this->getTranslations()->scanRootParentDirectory) {
                $root = dirname($root);
            }

            $directories[] = $root;
        } elseif (is_array($this->getTranslations()->root)) {
            foreach ($this->getTranslations()->root as $root) {
                $directories[] = Yii::getAlias($root);
            }
        } else {
            throw new InvalidConfigException('Invalid `root` option value!');
        }

        return $directories;
    }

    /**
     * @param $category
     * @return bool
     * @throws InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function isValidCategory($category)
    {
        return !in_array($category, $this->getTranslations()->ignoredCategories);
    }
}
