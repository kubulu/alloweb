<?php

namespace app\controllers;

use app\forms\MessageForm;
use app\helpers\Url;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class MessagesController extends \app\base\Controller
{
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
                    'create' => ['post'],
                    'delete' => ['post'],
                    'delete-conversation' => ['post'],
                    'read-conversation' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'user' => $this->getCurrentUser(),
            'profile' => $this->getCurrentUserProfile(),
        ]);
    }

    /**
     * @return bool
     * @throws \yii\base\ExitException
     */
    public function actionConversations()
    {
        $query = Yii::$app->request->get('query');
        $conversations = $this->messageManager->getConversations(Yii::$app->user->id, $query);

        return $this->sendJson([
            'success' => true,
            'conversations' => $conversations,
            'newMessagesCounts' => $this->messageManager->getNewMessagesCounts(Yii::$app->user->id),
        ]);
    }

    /**
     * @param $contactId
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionMessages($contactId)
    {
        $contact = $this->userManager->getUserById(Yii::$app->request->get('contactId'));
        if ($contact == null) {
            throw new NotFoundHttpException('Contact not found');
        }
        $messages = $this->messageManager->getMessages($contactId, Yii::$app->user->id);

        return $this->sendJson([
            'success' => true,
            'contact' => [
                'id' => $contact->id,
                'username' => $contact->username,
                'full_name' => $contact->profile->getDisplayName(),
                'avatar' => $contact->profile->getAvatarUrl(48, 48),
                'url' => Url::to(['/profile/view', 'username' => $contact->username]),
                'online' => $contact->isOnline,
                'verified' => (bool) $contact->profile->is_verified,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionCreate()
    {
        $contact = $this->getContactUser();
        $form = new MessageForm();
        $form->load(Yii::$app->request->post());

        if ($form->validate()) {
            $message = $this->messageManager->createMessage(Yii::$app->user->id, $contact->id, $form->message);
            if (!$message->isNewRecord) {
                $message->refresh();
                return $this->sendJson([
                    'success' => true,
                    'message' => Yii::t('app','Message has been sent'),
                    'messageId' => $message->id,
                    'pendingMessageId' => (int) Yii::$app->request->post('pendingMessageId'),
                ]);
            }
        }

        return $this->sendJson([
            'success' => false,
            'message' => Yii::t('app', $form->getErrorSummary(true)[0]),
            'pendingMessageId' => (int) Yii::$app->request->post('pendingMessageId'),
            'errors' => $form->errors,
        ]);
    }

    /**
     * @return bool
     * @throws \yii\base\ExitException
     */
    public function actionDelete()
    {
        $messageIds = Yii::$app->request->post('messages');
        $count = $this->messageManager->deleteMessages(Yii::$app->user->id, $messageIds);

        return $this->sendJson([
            'success' => true,
            'message' => Yii::t('app', 'Selected messages has been deleted'),
            'count' => $count,
        ]);
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionDeleteConversation()
    {
        $contact = $this->getContactUser(true);
        $success = $this->messageManager->deleteConversation(Yii::$app->user->id, $contact->id);

        return $this->sendJson([
            'success' => $success,
            'message' => Yii::t('app', 'Selected conversation has been deleted'),
        ]);
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionReadConversation()
    {
        $contact = $this->getContactUser();
        $success = $this->messageManager->readConversation(Yii::$app->user->id, $contact->id);

        return $this->sendJson([
            'success' => $success,
            'newMessagesCount' => $this->messageManager->getNewMessagesCounts(Yii::$app->user->id),
            'message' => Yii::t('app', 'Updated'),
        ]);
    }

    public function actionNewMessagesCounts()
    {
        return $this->sendJson($this->messageManager->getNewMessagesCounts(Yii::$app->user->id));
    }

    /**
     * @param bool $includeBanned
     * @return \app\models\User|array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function getContactUser($includeBanned = false)
    {
        $userId = Yii::$app->request->getQueryParam('contactId');
        if ($userId == null) {
            $userId = Yii::$app->request->getBodyParam('contactId');
        }
        $user = $this->userManager->getUserById($userId, ['includeBanned' => $includeBanned]);
        if ($user == null) {
            throw new NotFoundHttpException('Contact not found');
        }

        return $user;
    }
}
