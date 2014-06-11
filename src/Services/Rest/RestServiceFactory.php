<?php

namespace ShopwareCli\Services\Rest;

/**
 * Factory for cache decorated rest services
 *
 * Class RestServiceFactory
 * @package ShopwareCli\Plugin\Services\Rest
 */
class RestServiceFactory
{

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function factory($options, $cacheTime)
    {
        return new CacheDecorator(new RestClient($options), $this->container->get('cache'), $cacheTime);
    }
}