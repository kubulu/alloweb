<?php

namespace app\actions;

use yii\web\HttpException;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\actions
 */
class ErrorAction extends \yii\web\ErrorAction
{
    public function init()
    {
        parent::init();
        if ($this->exception instanceof HttpException) {
            switch ($this->exception->statusCode) {
                case 403:
                    $this->view = 'error/403';
                    break;
                case 404:
                    $this->view = 'error/404';
                    break;
                case 500:
                    $this->view = 'error/500';
                    break;
            }
        }
    }
}
