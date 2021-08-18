<?php

namespace mydate\components;

use yii\base\BootstrapInterface;

class ThemeBootstrap extends \youdate\components\ThemeBootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        // your customization code goes here
        // ...

        $this->initEvents();
    }

    public function initEvents()
    {
        // events
        // ...
    }
}
