<?php

namespace app\modules\admin\traits;

use app\modules\admin\components\Translations;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\traits
 */
trait TranslationsComponentTrait
{
    /**
     * @var Translations
     */
    private $_translationsComponent;

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getTranslations()
    {
        if (!isset($this->_translationsComponent)) {
            $this->_translationsComponent = Yii::$container->get(Translations::class);
        }

        return $this->_translationsComponent;
    }
}
