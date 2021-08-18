<?php

namespace app\managers;

use app\models\Profile;
use Yii;
use yii\base\Component;
use yii\data\ActiveDataProvider;
use app\models\Photo;
use app\models\query\PhotoQuery;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\managers
 */
class PhotoManager extends Component
{
    /**
     * @param $id
     * @param array $params
     * @return Photo|array|null
     */
    public function getPhoto($id, $params = [])
    {
        return $this->getQuery($params)->andWhere(['photo.id' => $id])->one();
    }

    /**
     * @param $photo Photo
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deletePhoto($photo)
    {
        if ($photo->delete()) {
            Yii::$app->photoStorage->delete($photo->source);
            return true;
        }

        return false;
    }

    /**
     * @param $userId
     * @param $photoId
     * @param array $params
     * @return Photo|array|null
     */
    public function getUserPhoto($userId, $photoId, $params = [])
    {
        return $this->getQuery($params)->andWhere(['photo.id' => $photoId, 'photo.user_id' => $userId])->one();
    }

    /**
     * @param $userId
     * @param null $photoId
     * @return bool
     * @throws \Exception
     */
    public function resetUserPhoto($userId, $photoId = null)
    {
        $profile = Profile::findOne(['user_id' => $userId]);
        if ($profile == null) {
            throw new \Exception('Profile not found');
        }
        if ($photoId == null) {
            $photo = $this->getQuery()->forUser($userId)->orderBy('id desc')->one();
            if ($photo == null) {
                return false;
            }
            $photoId = $photo->id;
        }

        $profile->photo_id = $photoId;

        return $profile->save();
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function getPhotosProvider($params = [])
    {
        $query = $this->getQuery($params);

        $dataProviderOptions = [
            'query' => $query,
        ];

        if (isset($params['pagination'])) {
            $dataProviderOptions['pagination'] = $params['pagination'];
        }

        return new ActiveDataProvider($dataProviderOptions);
    }

    /**
     * @return mixed
     */
    public function isVerificationEnabled()
    {
        return Yii::$app->settings->get('common', 'photoModerationEnabled');
    }

    /**
     * @param array $params
     * @return PhotoQuery
     */
    protected function getQuery($params = [])
    {
        if (!isset($params['verifiedOnly'])) {
            $params['verifiedOnly'] = $this->isVerificationEnabled();
        }

        $query = Photo::find()
            ->verified($params['verifiedOnly'])
            ->orderBy('photo.id desc');

        if (isset($params['userId'])) {
            $query->andWhere(['photo.user_id' => $params['userId']]);
        }

        return $query;
    }
}
