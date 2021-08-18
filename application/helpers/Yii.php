<?php

/**
 * IDE Auto-complete helper
 */

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static $app;
}

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @property \yii\mutex\MysqlMutex $mutex
 * @property \app\helpers\Geographer $geographer
 * @property \app\helpers\Emoji $emoji
 * @property \app\settings\Settings $settings
 * @property \app\components\AppMailer $appMailer
 * @property \app\managers\BalanceManager $balanceManager
 * @property \app\themes\ThemeManager $themeManager
 * @property \app\managers\UserManager $userManager
 * @property \app\managers\PhotoManager $photoManager
 * @property \app\managers\LikeManager $likeManager
 * @property \app\managers\GuestManager $guestManager
 * @property \app\managers\MessageManager $messageManager
 * @property \app\managers\NotificationManager $notificationManager
 * @property \app\files\Storage $photoStorage
 */
abstract class BaseApplication extends yii\base\Application
{
}

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @property \trntv\glide\components\Glide $glide
 * @property \app\models\User $user
 */
class WebApplication extends yii\web\Application
{
}

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class ConsoleApplication extends yii\console\Application
{
}
