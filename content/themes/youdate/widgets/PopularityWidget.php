<?php

namespace youdate\widgets;

use app\managers\GuestManager;
use Yii;
use yii\base\Widget;
use yii\behaviors\CacheableWidgetBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class PopularityWidget extends Widget
{
    /**
     * @var integer
     */
    public $userId;

    public function run()
    {
        /** @var GuestManager $manager */
        $manager = Yii::$app->guestManager;
        $popularity = $manager->calculatePopularity($this->userId);

        /** todo: make a better popularity algorithm **/
        $value = null;
        $color = null;
        switch ($popularity) {
            case GuestManager::POPULARITY_VERY_LOW:
                $value = 12;
                $color = 'red';
                break;
            case GuestManager::POPULARITY_LOW:
                $value = 37;
                $color = 'orange';
                break;
            case GuestManager::POPULARITY_MEDIUM:
                $value = 67;
                $color = 'green';
                break;
            case GuestManager::POPULARITY_HIGH:
                $value = 100;
                $color = 'blue';
        }

        return $this->render('popularity/widget', [
            'popularity' => $popularity,
            'value' => $value,
            'color' => $color,
            'title' => $manager->getPopularityTitle($popularity),
        ]);
    }
}
