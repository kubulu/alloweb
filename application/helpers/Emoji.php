<?php

namespace app\helpers;

use Yii;
use yii\base\Component;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\helpers
 */
class Emoji extends Component
{
    /**
     * @var string
     */
    public $smileMapFile = '@app/data/smiles.php';
    /**
     * @var string
     */
    public $emojiFile = '@app/data/emoji.php';
    /**
     * @var null|array
     */
    protected $smileMap = null;
    /**
     * @var null|array
     */
    protected $emoji = null;

    /**
     * @param $string
     * @return mixed
     */
    public function replaceSmilesToEmoji($string)
    {
        $map = $this->getSmilesMap();
        foreach ($map as $smile => $emoji) {
            $string = str_replace($smile, $emoji, $string);
        }

        return $string;
    }

    /**
     * @return mixed
     */
    public function getEmoji()
    {
        if (!isset($this->emoji)) {
            $file = Yii::getAlias($this->emojiFile);
            $this->emoji = require ($file);
        }

        return $this->emoji;
    }

    /**
     * @return mixed
     */
    protected function getSmilesMap()
    {
        if (!isset($this->smileMap)) {
            $file = Yii::getAlias($this->smileMapFile);
            $this->smileMap = require ($file);
        }

        return $this->smileMap;
    }
}
