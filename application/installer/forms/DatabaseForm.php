<?php

namespace installer\forms;

use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package installer\forms
 */
class DatabaseForm extends Model
{
    /**
     * @var string
     */
    public $dbHost = 'localhost';
    /**
     * @var string
     */
    public $dbDatabase = 'youdate';
    /**
     * @var string
     */
    public $dbUsername = '';
    /**
     * @var string
     */
    public $dbPassword = '';
    /**
     * @var string database charset
     */
    public $dbCharset = 'utf8mb4';

    public function rules()
    {
        return [
            [['dbHost', 'dbDatabase', 'dbUsername'], 'required'],
            [['dbHost', 'dbDatabase', 'dbUsername', 'dbPassword'], 'string'],
            [['dbPassword'], 'safe'],
        ];
    }

    /**
     * @return bool
     */
    public function setup()
    {
        $db = Yii::$app->db;
        $dbSettings = [
            'dsn' => sprintf('mysql:host=%s;dbname=%s', $this->dbHost, $this->dbDatabase),
            'username' => $this->dbUsername,
            'password' => $this->dbPassword,
            'charset' => $this->dbCharset,
        ];

        try {
            foreach ($dbSettings as $key => $value) {
                $db->$key = $value;
            }
            $db->open();
        } catch (Exception $e) {
            $this->addError('server', $e->getMessage());
            return false;
        }

        return true;
    }
}
