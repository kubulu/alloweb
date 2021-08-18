<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\components
 */
class AppState extends Component
{
    const STATUS_NORMAL = 'normal';
    const STATUS_MAINTENANCE = 'maintenance';

    /**
     * @var string
     */
    public $appStateFile = '@app/runtime/application.json';
    /**
     * @var array
     */
    protected $state;

    /**
     * @return array
     */
    public function getState()
    {
        if (!isset($this->state)) {
            $this->state = $this->readState();
        }

        return $this->state;
    }

    /**
     * @return array
     */
    public function readState()
    {
        $file = Yii::getAlias($this->appStateFile);
        if (!file_exists($file)) {
            $this->state = $this->writeState([
                'status' => self::STATUS_MAINTENANCE,
                'installedVersion' => null,
            ]);
        }

        try {
            $this->state = json_decode(file_get_contents($file), true);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return $this->state;
    }

    /**
     * @param $state
     * @return mixed
     */
    public function writeState($state = null)
    {
        $state = $state == null ? $this->state : $state;
        $file = Yii::getAlias($this->appStateFile);
        file_put_contents($file, json_encode($state, JSON_PRETTY_PRINT));

        return $state;
    }

    /**
     * @return bool
     */
    public function requiresUpdate()
    {
        $version = ArrayHelper::getValue($this->getState(), 'installedVersion');
        return $version === null || version_compare(version(), $version, '>');
    }

    /**
     * @return bool
     */
    public function isMaintenance()
    {
        $status = ArrayHelper::getValue($this->getState(), 'status', self::STATUS_NORMAL);
        return $status == self::STATUS_MAINTENANCE;
    }

    /**
     * @param $onOff
     */
    public function setMaintenance($onOff)
    {
        $this->state['status'] = $onOff === true ? self::STATUS_MAINTENANCE : self::STATUS_NORMAL;
        $this->writeState();
    }

    /**
     * @param null $version
     */
    public function updateVersion($version = null)
    {
        $this->state['installedVersion'] =  $version == null ? version() : $version;
        $this->writeState();
    }

    /**
     * @return void
     */
    public function resetState()
    {
        $this->state = [
            'status' => self::STATUS_NORMAL,
            'installedVersion' => version(),
        ];
        $this->writeState();
    }
}
