<?php

namespace app\themes;

use Yii;
use yii\base\ActionEvent;
use yii\base\Theme;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\i18n\DbMessageSource;
use yii\base\Controller;
use app\settings\Settings;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\themes
 */
class ThemeManager extends \yii\base\BaseObject implements BootstrapInterface
{
    /**
     * @var Settings component
     */
    public $settings = 'settings';
    /**
     * @var bool
     */
    public $isExtendedTheme = false;
    /**
     * @var string
     */
    protected $currentThemeId;
    /**
     * @var array
     */
    protected $currentThemeSettings;
    /**
     * @var array
     */
    protected $availableThemes;

    /**
     * @param \yii\base\Application $app
     * @throws InvalidConfigException
     * @throws Exception
     * @throws \Exception
     */
    public function bootstrap($app)
    {
        $themeId = $this->getCurrentThemeId();
        $theme = $this->getTheme($themeId);

        if ($theme == null) {
            throw new InvalidConfigException('App theme not set.');
        }

        $viewPathMap = [];
        $viewBasePath = Yii::getAlias("@content/themes/$themeId");
        $viewBaseUrl = Yii::getAlias("@web/content/themes/$themeId");

        // Check if theme is extended from other
        $extendedThemeId = isset($theme['extends']) ? $theme['extends'] : null;
        if ($extendedThemeId !== null) {
            $extendedTheme = $this->getTheme($extendedThemeId);
            if ($extendedTheme) {
                $this->isExtendedTheme = true;
                $extendedBasePath = Yii::getAlias("@content/themes/$extendedThemeId");
                $extendedBaseUrl = Yii::getAlias("@web/content/themes/$extendedThemeId");
                Yii::getAlias("@web/content/themes/$extendedThemeId");
                Yii::setAlias("@extendedTheme", $viewBasePath);
                Yii::setAlias('@extendedThemeUrl', $viewBaseUrl);
                Yii::setAlias('@' . $extendedTheme['namespace'], Yii::getAlias("@content/themes/$extendedThemeId"));
                $viewPathMap['@app/views'][] = '@extendedTheme/views';
                $viewBasePath = $extendedBasePath;
                $viewBaseUrl = $extendedBaseUrl;
            }
        }

        // Setup theme
        $namespace = isset($theme['namespace']) ? $theme['namespace'] : 'theme';
        Yii::setAlias("@theme", $viewBasePath);
        Yii::setAlias('@themeUrl', $viewBaseUrl);
        Yii::setAlias("@$namespace", Yii::getAlias("@content/themes/$themeId"));
        $viewPathMap['@app/views'][] = '@theme/views';

        $bootstrapClass = "$namespace\\components\\ThemeBootstrap";
        if (class_exists($bootstrapClass)) {
            /** @var BootstrapInterface $bootstrap */
            $bootstrap = new $bootstrapClass();
            $bootstrap->bootstrap($app);
        }

        // Setup Yii theme
        $app->view->theme = Yii::createObject([
            'class' => Theme::class,
            'basePath' => "@theme",
            'baseUrl' => "@themeUrl",
            'pathMap' => $viewPathMap,
        ]);

        // Setup translations
        $app->i18n->translations[$themeId . '*'] = Yii::createObject([
            'class' => DbMessageSource::class,
            'sourceLanguage' => 'en-US',
            'sourceMessageTable' => '{{%language_source}}',
            'messageTable' => '{{%language_translate}}',
            'cachingDuration' => 86400,
            'enableCaching' => !YII_DEBUG,
        ]);

        // Attach events
        Event::on(Controller::class, Controller::EVENT_BEFORE_ACTION, function(ActionEvent $event) use ($themeId) {
            $controller = $event->action->controller;
            $controller->view->params['frontend'] = Yii::$app->settings->get('frontend');
            $controller->view->params['theme'] = Yii::$app->settings->get('theme.' . $themeId);
        });
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCurrentThemeId()
    {
        if (isset($this->currentThemeId)) {
            return $this->currentThemeId;
        }

        $this->currentThemeId = $this->getSettings()->get('frontend', 'themeId');
        if ($this->currentThemeId == null) {
            $this->currentThemeId = env('APP_DEFAULT_THEME');
        }

        Yii::debug('Selected theme: ' . $this->currentThemeId);
        return $this->currentThemeId;
    }

    /**
     * @return array
     */
    public function getAvailableThemes()
    {
        if (isset($this->avaialbleThemes)) {
            return $this->availableThemes;
        }

        $themesDirectory = Yii::getAlias('@content/themes');
        $paths = scandir($themesDirectory);
        $themes = [];
        foreach ($paths as $path) {
            $themeId = basename($path);
            $fullPath = $themesDirectory . DIRECTORY_SEPARATOR . $path;
            $themeInfoPath = $fullPath . DIRECTORY_SEPARATOR . 'theme.json';
            if (is_dir($fullPath) && $path !== '.' && $path !== '..' && file_exists($themeInfoPath)) {
                try {
                    $themeInfo = json_decode(file_get_contents($themeInfoPath), true);
                    $screenshotPath = $fullPath . DIRECTORY_SEPARATOR . 'screenshot.png';
                    $screenshotUrl = Yii::getAlias("@web/content/themes/$themeId/screenshot.png");
                    $themes[$themeId] = array_merge($themeInfo, [
                        'screenshot' => is_file($screenshotPath) ? $screenshotUrl : null,
                    ]);
                } catch (\Exception $e) {
                }
            }
        }

        $this->availableThemes = $themes;
        Yii::debug('Available themes: ' . count($themes));

        return $themes;
    }

    /**
     * @param $themeId
     * @return mixed|null
     */
    public function getTheme($themeId)
    {
        $themes = $this->getAvailableThemes();

        return isset($themes[$themeId]) ? $themes[$themeId] : null;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getCurrentThemeInfo()
    {
        return $this->getTheme($this->getCurrentThemeId());
    }

    /**
     * @param $themeId
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function activate($themeId)
    {
        $themes = $this->getAvailableThemes();
        if (isset($themes[$themeId])) {
            $this->getSettings()->set('frontend', 'themeId', $themeId);
            return true;
        }

        return false;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getThemeSettings()
    {
        if (isset($this->currentThemeSettings)) {
            return $this->currentThemeSettings;
        }

        $info = $this->getCurrentThemeInfo();
        $className = sprintf('%s\\components\\ThemeSettings', isset($info['namespace']) ? $info['namespace'] : 'theme');
        if (class_exists($className)) {
            /** @var $settings ThemeSettings */
            $settings = new $className;
            $this->currentThemeSettings = $settings->getSettings();
        }

        return $this->currentThemeSettings;
    }

    /**
     * @return Settings
     */
    protected function getSettings()
    {
        return \Yii::$app->{$this->settings};
    }
}
