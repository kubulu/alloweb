<?php

namespace app\helpers;

use MenaraSolutions\Geographer\Contracts\PoliglottaInterface;
use MenaraSolutions\Geographer\Services\Poliglottas\English;
use MenaraSolutions\Geographer\Services\TranslationAgency;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\helpers
 */
class GeographerTranslator extends TranslationAgency
{
    /**
     * @param string $language
     * @return PoliglottaInterface
     */
    public function getTranslator($language)
    {
        // fallback to english
        if  (!isset($this->languages[$language])) {
            $this->languages[$language] = English::class;
        }

        if (!isset($this->translators[$language])) {
            $this->translators[$language] = new $this->languages[$language]($this);
        }

        return $this->translators[$language];
    }
}
