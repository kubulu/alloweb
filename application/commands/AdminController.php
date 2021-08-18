<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Admin;
use app\models\User;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\commands
 */
class AdminController extends Controller
{
    /**
     * Add user to administrators
     *
     * @param $user integer username or userID
     * @throws \yii\base\ExitException
     */
    public function actionAdd($user)
    {
        $user = $this->findUser($user);
        if (Admin::add($user)) {
            $this->stdout(sprintf("User #%d marked as an administrator\n", $user->id));
        } else {
            $this->stdout(sprintf("User #%d is already marked as an administrator\n", $user->id));
        }
    }

    /**
     * Remove user from administrators
     *
     * @param $userId
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionRemove($userId)
    {
        $user = $this->findUser($userId);
        if (Admin::remove($user)) {
            $this->stdout(sprintf("User #%d removed from administrators\n", $user->id));
        } else {
            $this->stdout(sprintf("User #%d is not an administrator\n", $user->id));
        }
    }

    /**
     * Find user by id or username/email
     *
     * @param $user integer username or user id
     * @return User
     * @throws \yii\base\ExitException
     */
    protected function findUser($user)
    {
        if (is_numeric($user)) {
            $user = User::findOne(['id' => $user]);
        } else {
            $user = User::find()
                ->orWhere(['username' => $user])
                ->orWhere(['email' => $user])
                ->one();
        }
        if ($user == null) {
            $this->stderr(sprintf("%s: user not found\n", $user));
            Yii::$app->end();
        }

        return $user;
    }
}
