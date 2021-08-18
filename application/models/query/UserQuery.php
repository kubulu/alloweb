<?php

namespace app\models\query;

use app\models\User;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class UserQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return $this
     */
    public function online()
    {
        return $this->andWhere('unix_timestamp() - user.last_login_at < :time', [
            'time' => Yii::$app->params['onlineThreshold']
        ]);
    }

    /**
     * @return $this
     */
    public function verified()
    {
        return $this->andWhere('profile.is_verified = 1');
    }

    public function withPhoto()
    {
        return $this->andWhere(['is not', 'profile.photo_id', null]);
    }

    /**
     * @return $this
     */
    public function premiumOnly()
    {
        return $this->joinWith('premium')
            ->andWhere('user_premium.premium_until > :time', [':time' => time()]);
    }

    /**
     * @param $latitude
     * @param $longitude
     * @param $distance
     * @return UserQuery
     */
    public function locatedNear($latitude, $longitude, $distance = null)
    {
        $this->addSelect('(6371 * acos( cos ( radians(' . $latitude . '))
                          * cos( radians( profile.latitude ) )
                          * cos( radians( profile.longitude ) - radians(' . $longitude . ') )
                          + sin ( radians(' . $latitude . ') )
                          * sin( radians( profile.latitude ) )
                        )) AS distance');

        if ($distance !== null) {
            $this->andHaving('distance <= :distance', [':distance' => $distance]);
        }

        return $this;
    }

    /**
     * @param int $daysCount
     * @return $this
     */
    public function newUsers($daysCount = 7)
    {
        $this->andWhere("user.created_at > unix_timestamp(date_sub(now(), interval $daysCount day))");
        $this->withPhoto();
        $this->orderBy('user.id desc');

        return $this;
    }
}
