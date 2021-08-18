<?php

namespace app\modules\admin\controllers;

use app\components\Maintenance;
use app\models\Currency;
use app\base\Model;
use app\models\Profile;
use app\models\Sex;
use app\settings\SettingsAction;
use app\traits\AjaxValidationTrait;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\controllers
 */
class SettingsController extends \app\modules\admin\components\Controller
{
    use AjaxValidationTrait;

    /**
     * @return array
     */
    public function actions()
    {
        $settings = Yii::$app->params['settings'];
        if (is_callable($settings)) {
            $settings = $settings();
        }

        return [
            'index' => [
                'class' => SettingsAction::class,
                'category' => 'frontend',
                'title' => Yii::t('app', 'Main settings'),
                'viewFile' => 'settings',
                'items' => $settings['main'],
            ],
            'photo' => [
                'class' => SettingsAction::class,
                'category' => 'common',
                'title' => Yii::t('app', 'Photo settings'),
                'viewFile' => 'settings',
                'items' => $settings['photos'],
            ],
            'payment' => [
                'class' => SettingsAction::class,
                'category' => 'common',
                'title' => Yii::t('app', 'Payment settings'),
                'viewFile' => 'settings_payment',
                'viewParams' => function() {
                    $currencies = Currency::find()->all();
                    return [
                        'currencies' => count($currencies) ? $currencies : [new Currency()],
                    ];
                },
                'items' => $settings['payment'],
            ],
            'prices' => [
                'class' => SettingsAction::class,
                'category' => 'common',
                'title' => Yii::t('app', 'Price settings'),
                'viewFile' => 'settings',
                'items' => $settings['prices'],
            ],
        ];
    }

    /**
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSettingsCurrencies()
    {
        /** @var Currency[] $modelsCurrencies */
        $modelsCurrencies = Currency::find()->all();

        $oldIDs = ArrayHelper::map($modelsCurrencies, 'id', 'id');
        $modelsCurrencies = Model::createMultiple(Currency::class, $modelsCurrencies);
        Model::loadMultiple($modelsCurrencies, Yii::$app->request->post());
        $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsCurrencies, 'id', 'id')));

        if (Model::validateMultiple($modelsCurrencies)) {
            if (!empty($deletedIDs)) {
                Currency::deleteAll(['id' => $deletedIDs]);
            }
            foreach ($modelsCurrencies as $currency) {
                $currency->save();
            }
        }

        Yii::$app->session->setFlash('success', Yii::t('app', 'Currencies have been updated'));
        return $this->redirect(['payment']);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGenders()
    {
        /** @var Sex[] $genders */
        $genders = Sex::find()->all();
        $gendersArray = count($genders) ? $genders : [new Sex()];

        if (Yii::$app->request->isPost) {
            $oldIDs = ArrayHelper::map($genders, 'id', 'id');
            $genders = Model::createMultiple(Sex::class, $genders);
            Model::loadMultiple($genders, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($genders, 'id', 'id')));
            $deletedGenders = ArrayHelper::getColumn(Sex::find()->where(['in', 'id', $deletedIDs])->all(), 'sex');
            if (Model::validateMultiple($genders)) {
                if (!empty($deletedIDs)) {
                    Sex::deleteAll(['id' => $deletedIDs]);
                    Profile::updateAll(['sex' => null], ['in', 'sex', $deletedGenders]);
                }
                foreach ($genders as $gender) {
                    $gender->save();
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Gender options have been updated'));
                return $this->refresh();
            }
        }

        return $this->render('genders', [
            'genders' => $gendersArray,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCachedData()
    {
        if (Yii::$app->request->isPost) {
            Maintenance::flushData();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Cached data has been deleted'));
            return $this->refresh();
        }

        return $this->render('cached-data');
    }
}
