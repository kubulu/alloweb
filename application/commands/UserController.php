<?php

namespace app\commands;

use app\models\User;
use yii\console\Controller;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\commands
 */
class UserController extends Controller
{
    /**
     * @param $user
     * @param $password
     * @throws \yii\base\Exception
     */
    public function actionPassword($user, $password)
    {
        $model = User::find()->where(['or',
            ['id' => $user],
            ['username' => $user],
            ['email' => $user]
        ])->one();

        $this->stdout(sprintf("- User #%d, %s %s\n", $model->id, $model->username, $model->email));
        if ($this->confirm('Change password?')) {
            $model->resetPassword($password);
        }
    }
}
