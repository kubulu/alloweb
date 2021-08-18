<?php

namespace app\files;

use Yii;
use League\Flysystem\Filesystem;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\files
 */
class Storage extends \trntv\filekit\Storage
{
    /**
     * @var string
     */
    public $path;
    
    public function init()
    {
        $absolutePath = $this->path = Yii::getAlias($this->path);
        $this->filesystem = function() use ($absolutePath) {
            $adapter = new \League\Flysystem\Adapter\Local($absolutePath);
            return new Filesystem($adapter);
        };
        parent::init();
    }

    /**
     * @param $path
     * @return string
     */
    public function getAbsolutePath($path)
    {
        return Yii::getAlias($this->path) . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * @param $path
     * @return string
     */
    public function getUrl($path)
    {
        return $this->baseUrl . '/' . $path;
    }

    /**
     * @param $path
     * @return bool
     */
    public function isFileExists($path)
    {
        return file_exists($this->getAbsolutePath($path));
    }

    /**
     * @param $path
     * @return array|bool|null
     */
    public function getImageSize($path)
    {
        if (!$this->isFileExists($path)) {
            return null;
        }

        return getimagesize($this->getAbsolutePath($path));
    }
}
