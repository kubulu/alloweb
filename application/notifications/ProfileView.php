<?php

namespace app\notifications;

use app\helpers\Url;
use Yii;
use app\helpers\Html;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
class ProfileView extends BaseNotification
{
    /**
     * @inheritdoc
     */
    public $viewName = 'notifications/profile-view';
    /**
     * @var int
     */
    public $sortOrder = 110;

    /**
     * @inheritdoc
     */
    public function category()
    {
        return new ProfileViewCategory();
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return Url::to(['/connections/guests']);
    }

    /**
     * @inheritdoc
     */
    public function getMailSubject()
    {
        return Yii::t('app', 'New profile view');
    }

    public function render()
    {
        return $this->html();
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        return Yii::t('app', '{name} viewed your profile.', [
            'name' => Html::tag('strong',
                Html::a(Html::encode($this->sender->profile->getDisplayName()),
                    ['/profile/view', 'username' => $this->sender->username])
            ),
        ]);
    }
}
