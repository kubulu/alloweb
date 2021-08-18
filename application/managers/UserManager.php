<?php

namespace app\managers;

use app\forms\UserSearchForm;
use app\helpers\Url;
use app\models\Block;
use app\models\Encounter;
use app\models\Profile;
use app\models\query\UserQuery;
use app\models\Spotlight;
use app\models\User;
use Yii;
use yii\base\Component;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\managers
 */
class UserManager extends Component
{
    /**
     * @param $username
     * @return null|\yii\db\ActiveRecord|User
     */
    public function getUser($username)
    {
        $query = $this->getQuery()
            ->andWhere(['username' => $username]);

        return $query->one();
    }

    /**
     * @param $id
     * @param array $params
     * @return User|array|null|\yii\db\ActiveRecord
     */
    public function getUserById($id, $params = [])
    {
        return $this->getQuery($params)
            ->andWhere(['user.id' => (int) $id])
            ->one();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function getUsersProvider($params)
    {
        $query = $this->getQuery();

        if (isset($params['searchForm']) && $params['searchForm'] instanceof UserSearchForm) {
            /** @var $model UserSearchForm */
            $model = $params['searchForm'];
            if (isset($model->sex)) {
                if ($model->sex != Profile::SEX_NOT_SET) {
                    $query->andWhere('profile.sex & ' . (int) $model->sex);
                }
            }
            if (isset($model->fromAge)) {
                $query->andWhere('year(now()) - year(profile.dob) >= :fromAge', [':fromAge' => $model->fromAge]);
            }
            if (isset($model->toAge)) {
                $query->andWhere('year(now()) - year(profile.dob) <= :toAge', [':toAge' => $model->toAge]);
            }
            if (isset($model->locationType)) {
                switch ($model->locationType) {
                    case UserSearchForm::LOCATION_TYPE_ADDRESS:
                        $query->andWhere(['profile.country' => $model->country, 'profile.city' => $model->city]);
                        break;
                    case UserSearchForm::LOCATION_TYPE_NEAR:
                        if (!isset($model->latitude) || !isset($model->longitude)
                            || $model->distance == UserSearchForm::DISTANCE_EVERYWHERE) {
                            break;
                        }
                        $query->locatedNear($model->latitude, $model->longitude, $model->distance);
                        break;
                }
            }
            if ($model->online) {
                $query->online();
            }
            if ($model->verified) {
                $query->verified();
            }
            if ($model->withPhoto) {
                $query->withPhoto();
            }
        }

        if (isset($params['hideCurrentUser'])) {
            $query->andWhere('user.id <> :user', [':user' => Yii::$app->user->id]);
        }

        $query->orderBy(['user_boost.boosted_at' => SORT_DESC, 'user.id' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @param $forUser User
     * @param $limit
     * @param array $ignoreIds
     * @return array
     */
    public function getEncounters($forUser, $limit, $ignoreIds = [])
    {
        /** @var User[] $users */
        $query = $this->getQuery()
            ->andWhere(['<>', 'user.id', $forUser->id])
            ->join('left join', Encounter::tableName(),
                'encounter.to_user_id = user.id and encounter.from_user_id = :forUserId',
                ['forUserId' => $forUser->id])
            ->andWhere('encounter.id is null')
            ->andHaving('photosCount > 0')
            ->limit($limit);

        if (isset($forUser->profile->looking_for_sex)) {
            if ($forUser->profile->sex != Profile::SEX_NOT_SET) {
                $query->andWhere('profile.sex & ' . (int) $forUser->profile->looking_for_sex);
            }
        }
        if (isset($forUser->profile->looking_for_from_age)) {
            $query->andWhere('year(now()) - year(profile.dob) >= :fromAge', [
                ':fromAge' => $forUser->profile->looking_for_from_age,
            ]);
        }
        if (isset($forUser->profile->looking_for_to_age)) {
            $query->andWhere('year(now()) - year(profile.dob) <= :toAge', [
                ':toAge' => $forUser->profile->looking_for_to_age,
            ]);
        }

        if (isset($forUser->profile->latitude) && isset($forUser->profile->longitude)) {
            $query->locatedNear($forUser->profile->latitude, $forUser->profile->longitude);
            $query->orderBy('distance asc');
        }

        if (is_array($ignoreIds) && count($ignoreIds) < 10) {
            $query->andWhere(['not in', 'user.id', $ignoreIds]);
        }

        /** @var User[] $users */
        $users = $query->all();
        $encounters = [];
        $counter = 0;
        $sexOptions = $forUser->profile->getSexModels();

        foreach ($users as $user) {
            $encounters[$counter] = [
                'user' => [
                    'id' => $user->id,
                    'isOnline' => $user->isOnline,
                    'isPremium' => $user->isPremium,
                    'isVerified' => $user->profile->is_verified,
                ],
                'profile' => [
                    'url' => Url::to(['/profile/view', 'username' => $user->username]),
                    'avatar' => $user->profile->getAvatarUrl(96, 96),
                    'displayName' => $user->profile->getDisplayName(),
                    'displayLocation' => $user->profile->getDisplayLocation(),
                    'age' => $user->profile->getAge(),
                    'sex' => $user->profile->sex,
                    'sexAlias' => $sexOptions[$user->profile->sex]->alias ?? null,
                    'sexIcon' => $sexOptions[$user->profile->sex]->icon ?? null,
                    'sexTitle' => $user->profile->getSexTitle(),
                    'statusTitle' => $user->profile->getStatusTitle(),
                    'lookingForTitle' => $user->profile->getLookingForTitle(),
                    'lookingForAgeTitle' => $user->profile->getLookingForAgeTitle(),
                    'description' => $user->profile->description,
                ],
            ];
            foreach ($user->photos as $photo) {
                $encounters[$counter]['photos'][] = [
                    'id' => $photo->id,
                    'url' => $photo->getUrl(),
                ];
            }
            $counter++;
        }

        return $encounters;
    }

    /**
     * @param $fromUser User
     * @param $toUser User
     * @param bool $liked
     * @return Encounter
     */
    public function createEncounter($fromUser, $toUser, $liked = false)
    {
        $encounter = new Encounter();
        $encounter->from_user_id = $fromUser->id;
        $encounter->to_user_id = $toUser->id;
        $encounter->is_liked = $liked;
        if ($encounter->save()) {
            $encounter->refresh();
        }

        return $encounter;
    }

    /**
     * @param $count
     * @return Spotlight[]|array|\yii\db\ActiveRecord[]
     */
    public function getSpotlightUsers($count)
    {
        return Spotlight::find()
            ->joinWith(['user', 'user.profile', 'photo'])
            ->where(['is', 'blocked_at', null])
            ->orderBy('spotlight.created_at desc')
            ->limit($count)
            ->all();
    }

    /**
     * @param $userId
     * @param $blockedUserId
     * @return bool
     */
    public function blockUser($userId, $blockedUserId)
    {
        $block = new Block();
        $block->from_user_id = $userId;
        $block->blocked_user_id = $blockedUserId;

        return $block->save();
    }

    /**
     * @param $userId
     * @param $blockedUserId
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function unBlockUser($userId, $blockedUserId)
    {
        $model = Block::findOne([
            'from_user_id' => $userId,
            'blocked_user_id' => $blockedUserId,
        ]);

        if ($model == null) {
            return false;
        }

        return (bool) $model->delete();
    }

    /**
     * @param $userId
     * @return array|\yii\db\ActiveRecord[]|Block[]
     */
    public function getBlockedUsers($userId)
    {
        return Block::find()
            ->where(['from_user_id' => $userId])
            ->with('blockedUser', 'blockedUser.profile')
            ->orderBy('block.id desc')
            ->all();
    }

    /**
     * @param $userId
     * @param $blockedUserId
     * @return bool
     */
    public function isUserBlocked($userId, $blockedUserId)
    {
        $block = Block::find()
            ->where(['from_user_id' => $userId, 'blocked_user_id' => $blockedUserId])
            ->one();

        return $block !== null;
    }

    /**
     * @param array $params
     * @return UserQuery
     */
    public function getQuery($params = [])
    {
        $verifiedPhotosOnly = Yii::$app->photoManager->isVerificationEnabled();
        $allPhotos = ArrayHelper::getValue($params, 'allPhotos', false);
        if ($allPhotos) {
            $verifiedPhotosOnly = false;
        }

        $query = User::find()
            ->select(['user.*', 'profile.*'])
            ->addSelect([
                'photosCount' => 'COUNT(photo.id)'
            ])
            ->leftJoin('photo as photoCount',
                'photoCount.user_id = user.id' . ($verifiedPhotosOnly ? ' and photoCount.is_verified = 1' : ''))
            ->joinWith(['profile', 'profile.photo', 'boost', 'premium'])
            ->groupBy(['user.id']);

        if (ArrayHelper::getValue($params, 'includeBanned', false) === false) {
            $query->andWhere(['is', 'blocked_at', null]);
        }

        return $query;
    }
}
