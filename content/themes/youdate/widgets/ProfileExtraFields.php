<?php

namespace youdate\widgets;

use app\models\Profile;
use app\models\ProfileExtra;
use Yii;
use yii\base\Widget;
use yii\behaviors\CacheableWidgetBehavior;
use yii\caching\DbQueryDependency;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class ProfileExtraFields extends Widget
{
    /**
     * @var Profile
     */
    public $profile;
    /**
     * @var string
     */
    public $viewFile = 'profile/extra-fields';

    public function behaviors()
    {
        return [
            'cache' => [
                'class' => CacheableWidgetBehavior::class,
                'cacheEnabled' => true, // !YII_DEBUG,
                'cacheDuration' => 3600,
                'cacheDependency' => [
                    'class' => DbQueryDependency::class,
                    'query' => ProfileExtra::find()
                        ->where(['user_id' => $this->profile->user])
                        ->select('sum(updated_at) as cache')
                        ->asArray()
                ],
                'cacheKeyVariations' => [
                    $this->profile->user_id,
                    Yii::$app->language,
                ],
            ],
        ];
    }

    public function run()
    {
        if (!isset($this->profile) || !$this->profile instanceof Profile) {
            return '';
        }

        return $this->render($this->viewFile, [
            'profile' => $this->profile,
        ]);
    }
}
