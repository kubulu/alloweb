<?php

namespace youdate\widgets;

use app\forms\SpotlightForm;
use app\models\Profile;
use app\models\Spotlight;
use app\models\User;
use Yii;
use yii\base\Widget;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class SpotlightWidget extends Widget
{
    /**
     * @var int
     */
    public $count;
    /**
     * @var Spotlight
     */
    public $spotlightUsers = null;
    /**
     * @var User
     */
    public $user;
    /**
     * @var Profile
     */
    public $profile;

    /**
     * @return string
     */
    public function run()
    {
        if ($this->spotlightUsers == null) {
            $this->spotlightUsers = Yii::$app->userManager->getSpotlightUsers($this->count);
        }

        $spotlightForm = new SpotlightForm();
        $spotlightForm->userId = $this->user->id;

        return $this->render('spotlight/horizontal', [
            'spotlightUsers' => $this->spotlightUsers,
            'user' => $this->user,
            'profile' => $this->profile,
            'spotlightForm' => $spotlightForm,
            'userPhotos' => $this->getUserPhotos(),
            'price' => Yii::$app->balanceManager->getSpotlightPrice(),
        ]);
    }

    /**
     * @return array
     */
    public function getUserPhotos()
    {
        $photos = [];
        foreach ($this->user->photos as $photo) {
            $photos[] = [
                'id' => $photo->id,
                'url' => $photo->getThumbnail(Profile::AVATAR_NORMAL, Profile::AVATAR_NORMAL)
            ];
        }

        return $photos;
    }
}
