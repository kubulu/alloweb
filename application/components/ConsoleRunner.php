<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\components
 */
class ConsoleRunner extends Component
{
    /**
     * @var string path to yii cli file
     */
    public $file = '@app/yii';
    /**
     * @var string The PHP binary path.
     */
    public $phpBinaryPath = PHP_BINDIR . '/php';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->file === null) {
            throw new InvalidConfigException('The "file" property must be set.');
        } else {
            $this->file = Yii::getAlias($this->file);
        }
    }

    /**
     * @param $cmd
     * @return bool
     */
    public function run($cmd)
    {
        $cmd = "{$this->phpBinaryPath} {$this->file} $cmd";
        $cmd = $this->isWindows() === true
            ? $cmd = "start /b {$cmd}"
            : $cmd = "{$cmd} > /dev/null 2>&1 &";
        pclose(popen($cmd, 'r'));

        return true;
    }

    /**
     * @return bool
     */
    protected function isWindows()
    {
        return PHP_OS == 'WINNT' || PHP_OS == 'WIN32';
    }
}
