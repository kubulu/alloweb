<?php

namespace youdate\widgets;

use Yii;
use yii\authclient\ClientInterface;
use yii\authclient\widgets\AuthChoice;
use yii\authclient\widgets\AuthChoiceAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class Connect extends AuthChoice
{
    /**
     * @var array|null
     */
    public $accounts;
    /**
     * @var array
     */
    public $options = [];
    /**
     * @var string
     */
    public $prepend = '';
    /**
     * @var string
     */
    public $append = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        AuthChoiceAsset::register(Yii::$app->view);
        if ($this->popupMode) {
            Yii::$app->view->registerJs("\$('#" . $this->getId() . "').authchoice();");
        }
        $this->options['id'] = $this->getId();
        echo Html::beginTag('div', $this->options);

        if (count($this->getClients())) {
            echo $this->prepend;
        }
    }

    public function run()
    {
        echo parent::run();
        if (count($this->getClients())) {
            echo $this->append;
        }
    }

    /**
     * @inheritdoc
     */
    public function createClientUrl($provider)
    {
        if ($this->isConnected($provider)) {
            return Url::to(['/settings/disconnect', 'id' => $this->accounts[$provider->getId()]->id]);
        } else {
            return parent::createClientUrl($provider);
        }
    }

    /**
     * @return string
     */
    protected function renderMainContent()
    {
        $items = [];
        foreach ($this->getClients() as $externalService) {
            $items[] = Html::tag('li', $this->clientLink($externalService));
        }
        return Html::tag('ul', implode('', $items), ['class' => 'auth-clients']);
    }

    /**
     * @param ClientInterface $client
     * @param null $text
     * @param array $htmlOptions
     * @return string
     */
    public function clientLink($client, $text = null, array $htmlOptions = [])
    {
        $viewOptions = $client->getViewOptions();

        if ($text === null) {
            $text = Html::tag('i', '', ['class' => 'fa fa-' . $client->getName()]);
        }
        if (!isset($htmlOptions['class'])) {
            $htmlOptions['class'] = 'btn btn-icon btn-' . $client->getName();
        }
        if (!isset($htmlOptions['title'])) {
            $htmlOptions['title'] = $client->getTitle();
        }
        Html::addCssClass($htmlOptions, ['widget' => 'auth-link']);

        if ($this->popupMode) {
            if (isset($viewOptions['popupWidth'])) {
                $htmlOptions['data-popup-width'] = $viewOptions['popupWidth'];
            }
            if (isset($viewOptions['popupHeight'])) {
                $htmlOptions['data-popup-height'] = $viewOptions['popupHeight'];
            }
        }
        return Html::a($text, $this->createClientUrl($client), $htmlOptions);
    }

    /**
     * @param ClientInterface $provider
     * @return bool
     */
    public function isConnected(ClientInterface $provider)
    {
        return $this->accounts != null && isset($this->accounts[$provider->getId()]);
    }
}
