<?php

namespace ShopwareCli\Services\Rest;

/**
 * General interfaces for REST classes
 *
 * Class RestInterface
 * @package ShopwareCli\Rest
 */
interface RestInterface
{
    public function get($url, $parameters = array(), $headers = array());

    public function post($url, $parameters = array(), $headers = array());
}
