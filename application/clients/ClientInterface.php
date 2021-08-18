<?php

namespace app\clients;

/**
 * Interface ClientInterface
 * @package app\clients
 */
interface ClientInterface extends \yii\authclient\ClientInterface
{
    /**
     * @return mixed|string|null
     */
    public function getEmail();

    /**
     * @return mixed|string|null
     */
    public function getUsername();
}
