<?php

namespace ShopwareCli\Services\Rest;

use ShopwareCli\Services\Rest\Curl\RestClient;

/**
 * Factory for cache decorated rest services
 *
 * Class RestServiceFactory
 * @package ShopwareCli\Services\Services\Rest
 */
class RestServiceFactory
{

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function factory($baseUrl, $username = null, $password = null, $cacheTime = 3600)
    {
        return new CacheDecorator(new RestClient($baseUrl, $username, $password), $this->container->get('cache'), $cacheTime);
    }
}
