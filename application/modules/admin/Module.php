<?php

namespace app\modules\admin;

use app\models\LanguageTranslate;
use app\modules\admin\components\translations\scanners\ScannerPhpFunction;
use Yii;
use yii\base\Event;
use yii\db\AfterSaveEvent;
use yii\i18n\DbMessageSource;
use yii\web\ErrorHandler;
use yii\web\ForbiddenHttpException;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin
 */
class Module extends \yii\base\Module
{
    /**
     * @throws ForbiddenHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        // disallow guests and non-admins
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin) {
            throw new ForbiddenHttpException('Access denied.');
        }

        //override default error handler
        $handler = new ErrorHandler(['errorAction' => env('ADMIN_PREFIX') . '/default/error']);
        Yii::$app->set('errorHandler', $handler);
        $handler->register();

        // translation manager
        $this->components = [
            'translations' => [
                'class' => \app\modules\admin\components\Translations::class,
                'root' => '@app',
                'scanRootParentDirectory' => true,
                'tmpDir' => '@runtime',
                'phpTranslators' => ['::t'],
                'patterns' => ['*.php'],
                'ignoredCategories' => ['yii'],
                'ignoredItems' => ['config'],
                'scanTimeLimit' => null,
                'searchEmptyCommand' => '!',
                'defaultExportStatus' => 1,
                'defaultExportFormat' => 'json',
                'scanners' => [
                    ScannerPhpFunction::class,
                ],
            ]
        ];

        Event::on(LanguageTranslate::class, LanguageTranslate::EVENT_AFTER_UPDATE, [$this, 'resetCache']);
        Event::on(LanguageTranslate::class, LanguageTranslate::EVENT_AFTER_INSERT, [$this, 'resetCache']);
    }

    /**
     * @param $event AfterSaveEvent
     */
    public function resetCache($event)
    {
        /** @var LanguageTranslate $model */
        $model = $event->sender;
        Yii::$app->cache->delete([
            DbMessageSource::class,
            $model->languageSource->category,
            $model->language
        ]);
    }
}
