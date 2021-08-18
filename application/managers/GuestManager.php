<?php

namespace app\managers;

use app\events\FromToUserEvent;
use app\models\Guest;
use app\models\query\GuestQuery;
use app\models\User;
use app\notifications\ProfileView;
use app\traits\EventTrait;
use Yii;
use yii\base\Component;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\managers
 */
class GuestManager extends Component
{
    use EventTrait;

    const EVENT_BEFORE_TRACK = 'beforeTrack';
    const EVENT_AFTER_TRACK = 'afterTrack';

    const POPULARITY_VERY_LOW = 'very-low';
    const POPULARITY_LOW = 'low';
    const POPULARITY_MEDIUM = 'medium';
    const POPULARITY_HIGH = 'high';

    /**
     * @param $fromUser User
     * @param $visitedUser User
     * @return Guest
     */
    public function createGuest($fromUser, $visitedUser)
    {
        $guest = new Guest();
        $guest->from_user_id = $fromUser->id;
        $guest->visited_user_id = $visitedUser->id;
        $guest->save();

        return $guest;
    }

    /**
     * @param $fromUser User
     * @param $visitedUser User
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function trackVisit($fromUser, $visitedUser)
    {
        $this->trigger(self::EVENT_BEFORE_TRACK, $this->getFromToUserEvent($fromUser, $visitedUser));

        if ($fromUser->id == $visitedUser->id) {
            return false;
        }

        if ($fromUser->isPremium) {
            if ($fromUser->premium->incognito_active) {
                return false;
            }
        }

        $guest = $this->getQuery()
            ->byUser($fromUser->id)
            ->forUser($visitedUser->id)
            ->one();

        if ($guest == null) {
            $guest = $this->createGuest($fromUser, $visitedUser);
            if ($guest->isNewRecord) {
                return false;
            }
        } else {
            if (!$guest->isVisitOld) {
                return false;
            }
        }

        $guest->touch('updated_at');
        $status = $guest->save();
        if ($status) {
            $this->trigger(self::EVENT_AFTER_TRACK, $this->getFromToUserEvent($fromUser, $visitedUser, $guest));
        }

        return $status;
    }

    /**
     * @param $config
     * @return ActiveDataProvider
     */
    public function getGuestsProvider($config)
    {
        $userId = ArrayHelper::remove($config, 'userId');

        $query = $this->getQuery()
            ->joinWith(['fromUser', 'fromUser.profile'])
            ->forUser($userId);

        $query->orderBy('guest.updated_at desc');

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @param $userId
     * @return integer
     */
    public function getGuestsCounter($userId)
    {
        return $this->getGuestsProvider(['userId' => $userId])->getTotalCount();
    }

    /**
     * @param $userId
     * @return string
     */
    public function calculatePopularity($userId)
    {
        $count = $this->getQuery()
            ->forUser($userId)
            ->andWhere('updated_at > UNIX_TIMESTAMP(NOW() - INTERVAL 7 DAY)')
            ->count();

        /** todo: make a better popularity algorithm **/
        if ($count < 3) {
            return self::POPULARITY_VERY_LOW;
        } elseif ($count >= 3 && $count <= 5) {
            return self::POPULARITY_LOW;
        } elseif ($count >= 6 && $count <= 9) {
            return self::POPULARITY_MEDIUM;
        } else {
            return self::POPULARITY_HIGH;
        }
    }

    /**
     * @param $popularity
     * @return string
     */
    public function getPopularityTitle($popularity)
    {
        switch ($popularity) {
            case self::POPULARITY_VERY_LOW:
                return Yii::t('app', 'Very low');
            case self::POPULARITY_LOW:
                return Yii::t('app', 'Low');
            case self::POPULARITY_MEDIUM:
                return Yii::t('app', 'Medium');
            case self::POPULARITY_HIGH:
                return Yii::t('app', 'High');
        }
    }

    /**
     * @return GuestQuery
     */
    public function getQuery()
    {
        return Guest::find();
    }
}
