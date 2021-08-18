<?php

namespace app\helpers;

use app\base\Event;
use MenaraSolutions\Geographer\City;
use MenaraSolutions\Geographer\Country;
use MenaraSolutions\Geographer\Earth;
use MenaraSolutions\Geographer\Services\DefaultManager;
use Yii;
use yii\base\Component;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\helpers
 */
class Geographer extends Component
{
    const EVENT_GET_COUNTRIES = 'getCountries';

    /**
     * @var Earth
     */
    private $earth;
    /**
     * @var Country[]
     */
    private $countries;
    /**
     * @var array
     */
    private $_countriesList;
    /**
     * @var DefaultManager
     */
    private $manager;

    public function init()
    {
        $this->manager = new DefaultManager();
        $this->manager->setTranslator(
            new GeographerTranslator($this->manager->getStoragePath(), $this->manager->getRepository())
        );
    }

    /**
     * @return array
     */
    public function getCountriesList()
    {
        if (isset($this->_countriesList)) {
            return $this->_countriesList;
        }

        $list = [];
        foreach ($this->getCountries() as $country) {
            /** @var $country Country */
            $list[$country->getCode()] = $country->getName();
        }

        $event = new Event(['extraData' => $list]);
        $this->trigger(self::EVENT_GET_COUNTRIES, $event);
        $this->_countriesList = $event->extraData;

        return $this->_countriesList;
    }

    /**
     * @param $code
     * @return null
     */
    public function getCountryName($code)
    {
        $list = $this->getCountriesList();
        if (isset($list[$code])) {
            return $list[$code];
        }

        return null;
    }

    /**
     * @param $code
     * @return bool
     */
    public function isValidCountryCode($code)
    {
        try {
            return $this->getCountries()->findOne(['code' => $code]) != null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $code
     * @return bool
     */
    public function isValidCityCode($code)
    {
        try {
            City::build($code, $this->manager);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $countryCode
     * @param $query
     * @return array
     */
    public function findCities($countryCode, $query)
    {
        $results = [];
        $country = $this->getEarth()->findOne(['code' => $countryCode]);
        if ($country !== null) {
            $states = $country->find();
            foreach ($states as $state) {
                $cities = $state->getCities()->sortBy('name');
                foreach ($cities as $city) {
                    if (strpos($city->getName(), $query) !== -1) {
                        $results[] = [
                            'value' => $city->getCode(),
                            'text' => $city->getName(),
                        ];
                    }
                }
            }
        }

        return $results;
    }

    /**
     * @param $code
     * @return null|string
     */
    public function getCityName($code)
    {
        try {
            $city = City::build($code, $this->manager);
            if ($city !== null) {
                return $city->setLocale(Yii::$app->language)->getName();
            }
        } catch (\Exception $e) {

        }

        return null;
    }

    /**
     * @return Earth|mixed
     */
    private function getEarth()
    {
        if (!isset($this->earth)) {
            $this->earth = new Earth();
            $this->earth->setManager($this->manager);
            $this->earth->setLocale(Yii::$app->language);
        }

        return $this->earth;
    }

    /**
     * @return \MenaraSolutions\Geographer\Collections\MemberCollection|Country[]
     */
    private function getCountries()
    {
        if (!isset($this->countries)) {
            $this->countries = $this->getEarth()->getCountries()->sortBy('name');
        }

        return $this->countries;
    }
}
