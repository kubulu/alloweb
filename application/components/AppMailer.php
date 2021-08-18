<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\mail\MailerInterface;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\components
 */
class AppMailer extends Component
{
    /**
     * @var string
     */
    public $viewPath = '@app/views/mail';
    /**
     * @var string|array
     */
    public $sender;
    /**
     * @var string
     */
    public $siteName;
    /**
     * @var MailerInterface
     */
    public $mailerComponent;

    public function init()
    {
        parent::init();
        $this->siteName = Yii::$app->settings->get('frontend', 'siteName', 'YouDate');
        if (!isset($this->sender)) {
            $this->sender = env('APP_MAIL_FROM');
        }
    }

    /**
     * @param $to
     * @param $subject
     * @param $view
     * @param array $params
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendMessage($to, $subject, $view, $params = [])
    {
        $mailer = $this->mailerComponent === null ? Yii::$app->mailer : Yii::$app->get($this->mailerComponent);
        $mailer->viewPath = $this->viewPath;
        $mailer->view = Yii::$app->view;

        return $mailer->compose(['html' => $view, 'text' => 'text/' . $view], $params)
            ->setTo($to)
            ->setFrom($this->sender)
            ->setSubject($subject)
            ->send();
    }
}
