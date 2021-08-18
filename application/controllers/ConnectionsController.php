<?php

namespace app\controllers;

use app\managers\LikeManager;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class ConnectionsController extends \app\base\Controller
{
    const ENCOUNTER_LIKE = 'like';
    const ENCOUNTER_SKIP = 'skip';

    /**
     * @var array
     */
    protected $counters;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'toggle' => ['post'],
                    'encounter-action' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();
        $this->counters = array_merge(
            $this->likeManager->getLikeCounters(Yii::$app->user->id),
            ['guests' => $this->guestManager->getGuestsCounter(Yii::$app->user->id)]
        );
    }

    /**
     * @param $type
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function actionLikes($type = LikeManager::TYPE_FROM_CURRENT_USER)
    {
        $types = [LikeManager::TYPE_FROM_CURRENT_USER, LikeManager::TYPE_TO_CURRENT_USER, LikeManager::TYPE_MUTUAL];
        if (!in_array($type, $types)) {
            throw new BadRequestHttpException();
        }

        return $this->render('likes', [
            'type' => $type,
            'dataProvider' => $this->likeManager->getUsersProvider([
                'type' => $type,
                'userId' => Yii::$app->user->id,
            ]),
            'counters' => $this->counters,
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionGuests()
    {
        return $this->render('guests', [
            'dataProvider' => $this->guestManager->getGuestsProvider([
                'userId' => Yii::$app->user->id,
            ]),
            'counters' => $this->counters,
        ]);
    }

    /**
     * @return string
     */
    public function actionEncounters()
    {
        return $this->render('encounters', [
            'counters' => $this->counters,
            'user' => $this->getCurrentUser(),
            'profile' => $this->getCurrentUserProfile(),
        ]);
    }

    /**
     * @param $more
     * @throws \yii\base\ExitException
     */
    public function actionGetEncounters($more)
    {
        $ignoredIds = Yii::$app->request->get('ignoredIds');
        $ignoredIds = explode(',', $ignoredIds);
        $encounters = $this->userManager->getEncounters($this->getCurrentUser(), (bool) $more ? 10 : 5, (array) $ignoredIds);

        return $this->sendJson([
            'success' => true,
            'encounters' => $encounters,
        ]);
    }

    /**
     * @param $action
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionEncounterAction($action)
    {
        $fromUser = $this->getCurrentUser();
        $toUser = $this->getTargetUser();
        $isMutual = false;

        if ($action == self::ENCOUNTER_LIKE) {
            $this->likeManager->createLike($fromUser, $toUser);
            $isMutual = $this->likeManager->isMutualLike($this->getCurrentUser(), $toUser);
        }

        $this->userManager->createEncounter($fromUser, $toUser, $action == self::ENCOUNTER_LIKE ? true : false);

        return $this->sendJson([
            'success' => true,
            'isMutual' => $isMutual,
        ]);
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\ExitException
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionToggleLike()
    {
        $toUser = $this->getTargetUser();
        $like = $this->likeManager->toggleLike($this->getCurrentUser(), $toUser);

        return $this->sendJson([
            'success' => true,
            'liked' => $like !== null,
        ]);
    }
}
