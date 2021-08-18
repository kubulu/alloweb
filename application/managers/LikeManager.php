<?php

namespace app\managers;

use app\models\Like;
use app\models\query\LikeQuery;
use app\models\User;
use app\traits\EventTrait;
use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\managers
 */
class LikeManager extends Component
{
    use EventTrait;

    const TYPE_FROM_CURRENT_USER = 'from-you';
    const TYPE_TO_CURRENT_USER = 'to-you';
    const TYPE_MUTUAL = 'mutual';

    const EVENT_BEFORE_CREATE_LIKE = 'beforeLike';
    const EVENT_AFTER_CREATE_LIKE = 'afterLike';

    /**
     * @param $fromUser User
     * @param $toUser User
     * @return Like
     * @throws \yii\base\InvalidConfigException
     */
    public function createLike($fromUser, $toUser)
    {
        $this->trigger(self::EVENT_BEFORE_CREATE_LIKE, $this->getFromToUserEvent($fromUser, $toUser));

        if (!$fromUser instanceof User || !$toUser instanceof User) {
            throw new InvalidArgumentException();
        }

        if ($fromUser->id == $toUser->id) {
            throw new InvalidArgumentException();
        }

        $like = new Like();
        $like->from_user_id = $fromUser->id;
        $like->to_user_id = $toUser->id;

        if ($like->save()) {
            $like->refresh();
            $this->trigger(self::EVENT_AFTER_CREATE_LIKE, $this->getFromToUserEvent($fromUser, $toUser, $like));
        }

        return $like;
    }

    /**
     * @param $fromUser
     * @param $toUser
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteLike($fromUser, $toUser)
    {
        $like = $this->getUserLike($fromUser, $toUser);
        if ($like && $like->delete()) {
            return true;
        }

        return false;
    }

    /**
     * @param $fromUser User
     * @param $toUser User
     * @return Like|array|null
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function toggleLike($fromUser, $toUser)
    {
        if ($fromUser->id == $toUser->id) {
            return null;
        }
        $like = $this->getUserLike($fromUser, $toUser);
        if ($like == null) {
            $like = $this->createLike($fromUser, $toUser);
            return $like;
        } else {
            $like->delete();
            return null;
        }
    }

    /**
     * @param $fromUser User
     * @param $toUser User
     * @return bool
     */
    public function isMutualLike($fromUser, $toUser)
    {
        return $this->getUserLike($fromUser, $toUser) !== null && $this->getUserLike($toUser, $fromUser) !== null;
    }

    /**
     * @param $fromUser User
     * @param $toUser User
     * @return Like|array|null
     */
    public function getUserLike($fromUser, $toUser)
    {
        return $this->getQuery()->whereUsersAre($fromUser->id, $toUser->id)->one();
    }

    /**
     * @param $params
     * @return \app\models\query\UserQuery
     * @throws \Exception
     */
    public function getUsersQuery($params)
    {
        /** @var UserManager $userManager */
        $userManager = Yii::$app->userManager;
        $userId = ArrayHelper::remove($params, 'userId');
        $type = ArrayHelper::remove($params, 'type');

        $query = $userManager->getQuery();
        switch ($type) {
            case self::TYPE_FROM_CURRENT_USER:
                $query->joinWith('toUserLikes');
                $query->andWhere(['like.from_user_id' => $userId]);
                break;
            case self::TYPE_TO_CURRENT_USER:
                $query->joinWith('fromUserLikes');
                $query->andWhere(['like.to_user_id' => $userId]);
                break;
            case self::TYPE_MUTUAL:
                $query->joinWith('toUserLikes');
                $query->join('inner join', 'like as likeMutual', 'likeMutual.from_user_id = like.to_user_id');
                $query->andWhere([
                    'likeMutual.to_user_id' => $userId,
                    'like.from_user_id' => $userId,
                ]);
                $query->groupBy(['user.id', 'like.to_user_id']);
                break;
            default:
                throw new \Exception($type);
        }

        $query->orderBy(isset($params['order']) ? $params['order'] : 'like.created_at desc');

        if (isset($params['limit'])) {
            $query->limit($params['limit']);
        }

        if (isset($params['onlineOnly'])) {
            $onlineThreshold = Yii::$app->params['onlineThreshold'];
            $query->andWhere("unix_timestamp() - user.last_login_at < $onlineThreshold");
        }

        return $query;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \Exception
     */
    public function getUsersProvider($params)
    {
        return new ActiveDataProvider([
            'query' => $this->getUsersQuery($params),
        ]);
    }

    /**
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public function getLikeCounters($userId)
    {
        /** TODO: caching */
        return [
            self::TYPE_FROM_CURRENT_USER => $this->getUsersProvider([
                'type' => self::TYPE_FROM_CURRENT_USER,
                'userId' => $userId,
            ])->getTotalCount(),
            self::TYPE_TO_CURRENT_USER => $this->getUsersProvider([
                'type' => self::TYPE_TO_CURRENT_USER,
                'userId' => $userId,
            ])->getTotalCount(),
            self::TYPE_MUTUAL => $this->getUsersProvider([
                'type' => self::TYPE_MUTUAL,
                'userId' => $userId,
            ])->getTotalCount(),
        ];
    }

    /**
     * @return LikeQuery
     */
    public function getQuery()
    {
        return Like::find();
    }
}
