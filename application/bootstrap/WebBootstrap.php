<?php

namespace app\bootstrap;

use app\base\View;
use app\components\AppState;
use app\events\FromToUserEvent;
use app\events\MessageEvent;
use app\helpers\Emoji;
use app\helpers\Url;
use app\jobs\SendNotification;
use app\managers\GuestManager;
use app\managers\LikeManager;
use app\managers\MessageManager;
use app\managers\NotificationManager;
use app\models\Language;
use app\models\Like;
use app\notifications\ProfileLike;
use app\notifications\ProfileView;
use Yii;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\web\Application;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\bootstrap
 */
class WebBootstrap extends CoreBootstrap
{
    /**
     * @param $app Application
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        $appState = new AppState();
        $appState->readState();

        // Check if update apply is required
        if ($appState->requiresUpdate()) {
            $appState->setMaintenance(true);
            $app->catchAll = ['site/apply-updates'];
        }

        // Setup aliases
        Yii::setAlias('@content', Yii::getAlias('@webroot/content'));

        $this->setupLanguage($app);
        $this->setupTimezone($app);
        $this->setupSiteUrl($app);

        // Events
        $this->initEvents();
    }

    protected function initEvents()
    {
        Event::on(MessageManager::class, MessageManager::EVENT_BEFORE_MESSAGE_CREATE, function(MessageEvent $event) {
            /** @var Emoji $emoji */
            $emoji = Yii::$app->emoji;
            $event->message->text = $emoji->replaceSmilesToEmoji($event->message->text);
        });
        Event::on(GuestManager::class, GuestManager::EVENT_AFTER_TRACK, function(FromToUserEvent $event) {
            $notification = ProfileView::instance()->from($event->fromUser)->source($event->relatedData);
            $notification->saveRecord($event->toUser);
            Yii::$app->queue->push(new SendNotification([
                'notification' => $notification,
                'receiverId' => $event->toUser->id,
            ]));
        });
        Event::on(LikeManager::class, LikeManager::EVENT_AFTER_CREATE_LIKE, function(FromToUserEvent $event) {
            /** @var NotificationManager $manager */
            $manager = Yii::$app->notificationManager;
            $isAlreadySent = $manager->isNotificationSent($event->fromUser, $event->toUser, ProfileLike::class);

            if (!$isAlreadySent) {
                $notification = ProfileLike::instance()->from($event->fromUser)->source($event->relatedData);
                $notification->saveRecord($event->toUser);
                Yii::$app->queue->push(new SendNotification([
                    'notification' => $notification,
                    'receiverId' => $event->toUser->id,
                ]));
            }
        });
        Event::on(View::class, View::EVENT_CUSTOM_HEADER, function(Event $event) {
            /** @var View $view */
            $view = $event->sender;
            echo $view->frontendSetting('siteHeaderCode');
        });
        Event::on(View::class, View::EVENT_CUSTOM_FOOTER, function(Event $event) {
            /** @var View $view */
            $view = $event->sender;
            echo $view->frontendSetting('siteFooterCode');
        });
    }

    /**
     * @param $app Application
     */
    protected function setupLanguage($app)
    {
        $autoDetect = $app->settings->get('frontend', 'siteLanguageAutodetect', false);
        $siteLanguage = $app->settings->get('frontend', 'siteLanguage', 'en-US');
        $languages = ArrayHelper::getColumn(Language::getLanguages(true, true), 'language_id');

        if (Yii::$app->user->isGuest) {
            if ($autoDetect) {
                $app->language = $app->request->getPreferredLanguage($languages);
            } else {
                $app->language = $siteLanguage;
            }
        } else {
            $userLanguage = Yii::$app->user->identity->profile->getLanguage();
            if ($autoDetect && $userLanguage == null) {
                $app->language = $app->request->getPreferredLanguage($languages);
                return;
            }
            if ($userLanguage !== null) {
                $app->language = $userLanguage;
                return;
            }
            $app->language = $siteLanguage;
        }
    }

    /**
     * @param $app Application
     */
    protected function setupTimezone($app)
    {
        $timeZone = $app->settings->get('frontend', 'siteTimezone', 'UTC');
        if (!Yii::$app->user->isGuest) {
            $userTimeZone = Yii::$app->user->identity->profile->timezone;
            if ($userTimeZone !== null ) {
                $timeZone = $userTimeZone;
            }
        }
        if ($timeZone) {
            $app->formatter->timeZone = $timeZone;
            $app->setTimeZone($timeZone);
        }
    }

    /**
     * @param $app
     */
    protected function setupSiteUrl($app)
    {
        $siteUrl = $app->settings->get('common', 'siteUrl');
        if ($siteUrl == null) {
            $app->settings->set('common', 'siteUrl', Url::to(['/'], true));
        }
    }
}
