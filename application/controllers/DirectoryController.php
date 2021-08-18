<?php

namespace app\controllers;

use app\forms\UserSearchForm;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class DirectoryController extends \app\base\Controller
{
    /**
     * Main page
     *
     * @return string
     */
    public function actionIndex()
    {
        $params = [];

        $searchForm = new UserSearchForm();
        $searchForm->setProfile($this->getCurrentUserProfile());
        $searchForm->load(Yii::$app->request->get());
        $params['searchForm'] = $searchForm;
        $currentProfile = $this->getCurrentUserProfile();

        $currentCity = null;
        if (!Yii::$app->user->isGuest) {
            $params['hideCurrentUser'] = true;
            $city = isset($searchForm->city) || $currentProfile === null ? $searchForm->city : $currentProfile->city;
            $currentCity = [
                'value' => $city,
                'title' => Yii::$app->geographer->getCityName($city),
            ];
        }

        return $this->render('index', [
            'dataProvider' => $this->userManager->getUsersProvider($params),
            'user' => $this->getCurrentUser(),
            'profile' => $this->getCurrentUserProfile(),
            'searchForm' => $searchForm,
            'countries' => Yii::$app->geographer->getCountriesList(),
            'currentCity' => $currentCity,
        ]);
    }
}
