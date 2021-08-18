<?php

namespace youdate\widgets;

use app\forms\UserSearchForm;
use app\models\User;
use Yii;
use yii\base\Widget;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class DirectorySearchForm extends Widget
{
    /**
     * @var string
     */
    public $view = 'directory-search-form/widget';
    /**
     * @var User logged-in user
     */
    public $user;
    /**
     * @var UserSearchForm
     */
    public $model;
    /**
     * @var array
     */
    public $countries;
    /**
     * @var string
     */
    public $showMoreCookie = 'directory-search-more-visible';
    /**
     * @var array
     */
    public $currentCity = ['value' => null, 'title' => null];

    public function run()
    {
        return $this->render($this->view, [
            'model' => $this->model,
            'user' => $this->user,
            'countries' => $this->countries,
            'currentCity' => $this->currentCity,
            'showMoreCookie' => $this->showMoreCookie,
            'showMoreVisible' => isset($_COOKIE[$this->showMoreCookie]) ? (bool) $_COOKIE[$this->showMoreCookie] : false,
        ]);
    }
}
