<?php

namespace app\forms;

use app\managers\UserManager;
use app\models\User;
use Yii;
use yii\web\Application as WebApplication;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class MessageForm extends \yii\base\Model
{
    /**
     * @var integer
     */
    public $contactId;
    /**
     * @var string
     */
    public $message;

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['contactId', 'message'], 'required'],
            ['contactId', 'exist',
                'targetClass' => User::class,
                'targetAttribute' => ['contactId' => 'id']
            ],
            ['message', 'string', 'min' => 1, 'max' => 2000],
        ];

        if (Yii::$app instanceof WebApplication) {
            $rules[] = ['contactId', 'compare', 'compareValue' => Yii::$app->user->id, 'operator' => '!='];
            $rules[] = ['contactId', 'checkBlock'];
        }

        return $rules;
    }

    /**
     * @return bool
     */
    public function checkBlock()
    {
        /** @var UserManager $userManager */
        $userManager = Yii::$app->userManager;
        if ($userManager->isUserBlocked($this->contactId, Yii::$app->user->id)) {
            $this->addError('contactId', Yii::t('app', 'You are not allowed to send messages to this user'));
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }
}
