<?php

namespace app\modules\admin\components\translations\scanners;

use app\modules\admin\traits\TranslationsComponentTrait;
use Yii;
use yii\helpers\Console;
use yii\base\InvalidConfigException;
use app\modules\admin\components\translations\Scanner;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\components\translations\scanners
 */
class ScannerDatabase
{
    use TranslationsComponentTrait;

    /**
     * @var array array containing the table ids to process.
     */
    private $_tables;

    /**
     * @var Scanner object containing the detected language elements
     */
    private $_scanner;

    /**
     * ScannerDatabase constructor.
     * @param Scanner $scanner
     * @throws InvalidConfigException
     */
    public function __construct(Scanner $scanner)
    {
        $this->_scanner = $scanner;
        $this->_tables = $this->getTranslations()->tables;

        if (!empty($this->_tables) && is_array($this->_tables)) {
            foreach ($this->_tables as $tables) {
                if (empty($tables['table'])) {
                    throw new InvalidConfigException('Incomplete database  configuration: table ');
                } elseif (empty($tables['messageAttribute'])) {
                    throw new InvalidConfigException('Incomplete database  configuration: messageAttribute');
                }
            }
        }
    }

    /**
     * @throws \yii\db\Exception
     */
    public function run()
    {
        $this->_scanner->stdout('Detect DatabaseTable - BEGIN', Console::FG_GREY);
        if (is_array($this->_tables)) {
            foreach ($this->_tables as $tables) {
                $this->_scanningTable($tables);
            }
        }

        $this->_scanner->stdout('Detect DatabaseTable - END', Console::FG_GREY);
    }

    /**
     * @param $table
     * @throws \yii\db\Exception
     */
    private function _scanningTable($table)
    {
        $this->_scanner->stdout('Extracting messages from ' . $table['table'], Console::FG_GREEN);
        $query = new \yii\db\Query();
        $category = null;
        if ($table['categoryAttribute'] === false) {
            $category = 'app';
            $query->select($table['messageAttribute']);
        } else {
            $query->select([$table['categoryAttribute'], $table['messageAttribute']]);
        }
        $data = $query->from($table['table'])
            ->createCommand(Yii::$app->db)
            ->queryAll();
        foreach ($data as $item) {
            try {
                $this->_scanner->addLanguageItem(
                    $category == null ? $item[$table['categoryAttribute']] : $category,
                    $item[$table['messageAttribute']]
                );
            } catch (\Exception $e) {
                Yii::error($e->getMessage());
            }
        }
    }
}
