<?php

namespace app\forms;

use app\models\UserPremium;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class PremiumSettingsForm extends \yii\base\Model
{
    /**
     * @var bool
     */
    public $incognitoActive;
    /**
     * @var bool
     */
    public $showOnlineStatus;

    /**
     * @param UserPremium $premium
     * @return PremiumSettingsForm
     */
    public static function fromUserPremium($premium)
    {
        $form = new static();
        if ($premium !== null) {
            $form->incognitoActive = $premium->incognito_active;
            $form->showOnlineStatus = $premium->show_online_status;
        }

        return $form;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['incognitoActive', 'showOnlineStatus'], 'boolean'],
            [['incognitoActive', 'showOnlineStatus'], 'default', 'value' => true],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'incognitoActive' => Yii::t('app', 'Incognito mode'),
            'showOnlineStatus' => Yii::t('app', 'Show online status'),
        ];
    }
}
