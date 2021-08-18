<?php

namespace custom\controllers;

use app\base\Controller;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package custom\controllers
 */
class CustomController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
