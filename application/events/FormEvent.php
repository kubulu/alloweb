<?php

namespace app\events;

use app\base\Event;
use yii\base\Model;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\events
 * @property Model $model
 */
class FormEvent extends Event
{
    /**
     * @var Model
     */
    private $_form;

    /**
     * @return Model
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @param Model $form
     */
    public function setForm(Model $form)
    {
        $this->_form = $form;
    }
}
