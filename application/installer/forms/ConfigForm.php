<?php

namespace installer\forms;

use app\models\Admin;
use app\models\Profile;
use app\models\User;
use Yii;
use yii\base\Model;
use yii\base\Security;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package installer\forms
 */
class ConfigForm extends Model
{
    const MAILER_FILE = 'file';
    const MAILER_SENDMAIL = 'sendmail';
    const MAILER_SMTP = 'smtp';
    const MAILER_PHP_MAIL = 'mail';

    public $adminUsername = 'admin';
    public $adminEmail;
    public $adminPassword;
    public $adminPrefix = 'admin';
    public $appUrl;
    public $appMailerTransport;
    public $appMailerHost;
    public $appMailerUsername;
    public $appMailerPassword;
    public $appMailerPort = 465;
    public $appMailerEncryption = 'ssl';
    public $appMailFrom;
    public $facebookAppId;
    public $facebookAppSecret;
    public $twitterConsumerKey;
    public $twitterConsumerSecret;
    public $dbHost;
    public $dbUsername;
    public $dbPassword;
    public $dbDatabase;
    public $dbCharset;

    public $writableDirectories = [
        'application/runtime',
        'content/assets',
        'content/cache',
        'content/images',
        'content/pages',
        'content/photos',
    ];

    private $envTemplate;

    public function rules()
    {
        return [
            [['adminUsername', 'adminEmail', 'adminPassword'], 'required'],
            [['adminUsername'], 'string', 'min' => 3],
            [['adminPassword'], 'string', 'min' => 6],
            [['adminEmail', 'appMailFrom'], 'email'],
            [['appUrl'], 'url'],
            [['appMailerTransport', 'appMailFrom', 'adminPrefix'], 'required'],
            [['appMailerTransport', 'adminPrefix'], 'required'],
            [['appMailerTransport'], 'in', 'range' => [
                self::MAILER_FILE,
                self::MAILER_PHP_MAIL,
                self::MAILER_SENDMAIL,
                self::MAILER_SMTP,
            ]],
            [['appMailerHost', 'appMailerUsername', 'appMailerPassword', 'appMailerPort', 'appMailerEncryption'],
                'required',
                'when' => function(ConfigForm $model) {
                    return $model->appMailerTransport == self::MAILER_SMTP;
                }
            ],
            [['dbHost', 'dbDatabase', 'dbUsername', 'dbPassword', 'dbCharset'], 'safe'],
            [['facebookAppId', 'facebookAppSecret', 'twitterConsumerKey', 'twitterConsumerSecret'], 'safe'],
        ];
    }

    public function setTemplate($template)
    {
        $this->envTemplate = $template;
    }

    /**
     * @return static
     * @throws \yii\base\Exception
     */
    public function getConfig()
    {
        $vars = [];
        preg_match_all('^\{{(.*?)\}}^', $this->envTemplate, $vars);

        $config = $this->envTemplate;

        foreach ($vars[0] as $variable) {
            $attribute = str_replace('{', '', $variable);
            $attribute = str_replace('}', '', $attribute);
            if (isset($this->$attribute)) {
                $config = str_replace($variable, $this->$attribute, $config);
            }
            if ($attribute == 'generateRandom') {
                $random = (new Security())->generateRandomString(32);
                $config = str_replace($variable, $random, $config);
            }
        }

        return $config;
    }

    public function createUser()
    {
        $user = new User();
        $user->username = $this->adminUsername;
        $user->email = $this->adminEmail;
        $user->password = $this->adminPassword;
        $user->confirmed_at = time();
        $user->save();
        $user->refresh();

        $user->profile->status = Profile::STATUS_NOT_SET;
        $user->profile->is_verified = true;
        $user->profile->save();

        $admin = new Admin();
        $admin->link('user', $user);
    }

    public function makeWritable()
    {
        foreach ($this->writableDirectories as $directory) {
            $this->chmodRecursive(Yii::$app->params['basePath'] . '/' . $directory);
        }
    }

    /**
     * @param $path
     */
    private function chmodRecursive($path)
    {
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $item) {
            chmod($item->getPathname(), 0775);
            if ($item->isDir() && !$item->isDot()) {
                $this->chmodRecursive($item->getPathname());
            }
        }
    }
}
