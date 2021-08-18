<?php

namespace app\bootstrap;

use app\commands\CronController;
use app\components\AppMailer;
use app\models\Encounter;
use app\models\Notification;
use app\models\Upload;
use app\models\User;
use app\models\UserBoost;
use app\models\UserPremium;
use Yii;
use yii\base\Event;
use yii\console\Application;
use yii\web\UrlManager;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\bootstrap
 */
class ConsoleBootstrap extends CoreBootstrap
{
    /**
     * @param $app Application
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        // Setup aliases
        Yii::setAlias('@web', '/');
        Yii::setAlias('@webroot', Yii::getAlias('@app') . '/../');
        Yii::setAlias('@content', Yii::getAlias('@webroot/content'));

        // Setup website URL (for console commands, cron and queue processing)
        $siteUrl = env('APP_URL');
        if ($siteUrl === false) {
            $siteUrl = $app->settings->get('common', 'siteUrl');
        }
        $siteUrl = rtrim($siteUrl, '/') . '/';
        Yii::$container->set(UrlManager::class, [
            'baseUrl' => $siteUrl,
            'hostInfo' => $siteUrl,
        ]);

        // Delete unlinked photos
        Event::on(CronController::class, CronController::EVENT_ON_HOURLY_RUN, function(Event $event) {
            $controller = $event->sender;
            $photos = Upload::deleteAll('(unix_timestamp() - created_at) > 3600');
            $controller->stdout(sprintf("- Removed %d unlinked photos\n", $photos));
        });

        // Delete expired search boosts
        Event::on(CronController::class, CronController::EVENT_ON_HOURLY_RUN, function(Event $event) {
            $controller = $event->sender;
            $expiredBoosts = UserBoost::deleteAll('boosted_until < unix_timestamp()');
            $controller->stdout(sprintf("- Removed %d expired search boosts\n", $expiredBoosts));
        });

        // Auto boost premium users daily
        Event::on(CronController::class, CronController::EVENT_ON_DAILY_RUN, function(Event $event) {
            $controller = $event->sender;
            $premiumUsers = User::find()->premiumOnly()->all();
            foreach ($premiumUsers as $user) {
                $boostDuration = Yii::$app->balanceManager->getBoostDuration();
                UserBoost::boostUser($user->id, $boostDuration);
            }
            $controller->stdout(sprintf("- Boosted %d premium users\n", count($premiumUsers)));
        });

        // Delete expired premiums
        Event::on(CronController::class, CronController::EVENT_ON_HOURLY_RUN, function(Event $event) {
            $controller = $event->sender;
            $expiredPremiums = UserPremium::deleteAll('premium_until < unix_timestamp()');
            $controller->stdout(sprintf("- Removed %d expired premiums\n", $expiredPremiums));
        });

        // Delete expired encounters
        Event::on(CronController::class, CronController::EVENT_ON_DAILY_RUN, function(Event $event) {
            $controller = $event->sender;
            $expiredEncountersThreshold = Yii::$app->params['expiredEncountersThreshold'];
            $expiredPremiums = Encounter::deleteAll(
                "created_at < unix_timestamp(date_sub(now(), interval $expiredEncountersThreshold day))"
            );
            $controller->stdout(sprintf("- Removed %d expired encounters\n", $expiredPremiums));
        });

        // Delete expired notifications
        Event::on(CronController::class, CronController::EVENT_ON_DAILY_RUN, function(Event $event) {
            $controller = $event->sender;
            $expiredNotificationsThreshold = Yii::$app->params['expiredNotificationsThreshold'];
            $expiredPremiums = Notification::deleteAll(
                "created_at < unix_timestamp(date_sub(now(), interval $expiredNotificationsThreshold day))"
            );
            $controller->stdout(sprintf("- Removed %d old notifications\n", $expiredPremiums));
        });
    }
}
