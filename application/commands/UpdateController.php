<?php

namespace app\commands;

use app\components\AppState;
use app\components\ConsoleRunner;
use yii\console\Controller;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\commands
 */
class UpdateController extends Controller
{
    public function actionApply()
    {
        $consoleRunner = new ConsoleRunner(['file' => '@app/yii']);
        $appState = new AppState();
        $appState->readState();

        $appState->setMaintenance(true);
        $consoleRunner->run('migrate/up --interactive=0');
        $consoleRunner->run('cache/flush-all');

        $appState->setMaintenance(false);
        $appState->updateVersion();
    }
}
