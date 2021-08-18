<?php

namespace custom\components;

use app\base\Event;
use app\events\ProfileEvent;
use app\helpers\Url;
use app\models\Profile;
use custom\controllers\CustomController;
use yii\base\BootstrapInterface;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package custom\components
 */
class ThemeBootstrap extends \youdate\components\ThemeBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        parent::bootstrap($app);
        $app->controllerMap['custom'] = CustomController::class;
        $app->urlManager->addRules([
            'demo-controller' => 'custom/index',
        ]);

        $this->initEvents();
    }

    public function initEvents()
    {
        // Change avatar fallback from Gravatar to custom png:
        Event::on(Profile::class, Profile::EVENT_AVATAR_FALLBACK, function(ProfileEvent $event) {
            $avatar = null;
            switch ($event->getProfile()->sex) {
                case Profile::SEX_NOT_SET:
                    $avatar = '@extendedThemeUrl/static/images/sex-neutral.png';
                    break;
                case Profile::SEX_MALE:
                    $avatar = '@extendedThemeUrl/static/images/sex-male.png';
                    break;
                case Profile::SEX_FEMALE:
                    $avatar = '@extendedThemeUrl/static/images/sex-female.png';
                    break;
            }
            $event->getProfile()->fallbackAvatar = Url::to($avatar);
        });
    }
}
