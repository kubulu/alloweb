<?php

namespace app\modules\admin\components;

use Yii;
use yii\base\BaseObject;
use yii\web\Response;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\components
 */
class Translations extends BaseObject
{
    /**
     * Session key for storing front end translating privileges.
     */
    const SESSION_KEY_ENABLE_TRANSLATE = 'frontendTranslation_EnableTranslate';
    /**
     * @var array list of the categories being ignored.
     */
    public $ignoredCategories = [];
    /**
     * @var array directories/files being ignored.
     */
    public $ignoredItems = [
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/messages',
        '/BaseYii.php',
        'runtime',
        'bower',
        'nikic',
        'vendor',
        'node_modules',
    ];
    /**
     * @var string|array
     */
    public $root = [
        '@app',
        '@content/themes',
    ];

    /**
     * @var bool
     */
    public $scanRootParentDirectory = true;
    /**
     * @var string writable directory used for keeping the generated javascript files.
     */
    public $tmpDir = '@runtime/';
    /**
     * @var array list of file extensions that contain language elements.
     * Only files with these extensions will be processed.
     */
    public $patterns = ['*.php', '*.js'];
    /**
     * @var string name of the subdirectory which contains the language elements.
     */
    public $subDir = '/translate/';
    /**
     * @var array List of the PHP function for translating messages.
     */
    public $phpTranslators = ['::t'];
    /**
     * @var string PHP Regular expression to match arrays containing language elements to translate.
     */
    public $patternArrayTranslator = '#\@translate[^\$]+(?P<translator>[\w\d\s_]+[^\(\[]+)#s';
    /**
     * @var int The max_execution_time used when scanning, when set to null the default max_execution_time will not be modified.
     */
    public $scanTimeLimit = null;
    /**
     * @var array
     */
    public $tables = [
        [
            'table' => '{{%profile_field_category}}',
            'categoryAttribute' => 'language_category',
            'messageAttribute' => 'title',
        ],
        [
            'table' => '{{%profile_field}}',
            'categoryAttribute' => 'language_category',
            'messageAttribute' => 'title',
        ],
        [
            'table' => '{{%sex}}',
            'categoryAttribute' => false,
            'messageAttribute' => 'title',
        ],
        [
            'table' => '{{%sex}}',
            'categoryAttribute' => false,
            'messageAttribute' => 'title_plural',
        ],
        [
            'table' => '{{%currency}}',
            'categoryAttribute' => false,
            'messageAttribute' => 'title',
        ],
    ];
    /**
     * @var string The database table storing the languages.
     */
    public $languageTable = '{{%language}}';
    /**
     * @var string The search string to find empty translations.
     */
    public $searchEmptyCommand = '!';
    /**
     * @var int The minimum status for a language to be selected by default in the export list.
     */
    public $defaultExportStatus = 1;
    /**
     * @var string The default export format (yii\web\Response::FORMAT_JSON or yii\web\Response::FORMAT_XML).
     */
    public $defaultExportFormat = Response::FORMAT_JSON;
    /**
     * @var string The default db connection
     */
    public $connection = 'db';
    /**
     * @var array Scanners can be overriden here. If not set original set of scanners will be used from Scanner
     */
    public $scanners = [];

    /**
     * @return string The full path of the directory containing the generated JavaScript files.
     */
    public function getLanguageItemsDirPath()
    {
        return Yii::getAlias($this->tmpDir) . $this->subDir;
    }
}
