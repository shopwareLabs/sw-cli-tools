<?php

namespace ShopwareCli\Services\Rest;

use ShopwareCli\Cache\CacheInterface;

/**
 * Decorated a RestInterface in order to implement a simple cache layer for GET requests
 *
 * Class CacheDecorator
 * @package ShopwareCli\Services\Rest
 */
class CacheDecorator implements RestInterface
{
    /**
     * @var RestInterface
     */
    protected $decorate;

    /**
     * @var \ShopwareCli\Cache\CacheInterface
     */
    protected $cacheProvider;

    /**
     * @var int
     */
    protected $cacheTime;

    /**
     * @param RestInterface $restService
     * @param CacheInterface $cacheProvider
     * @param int $cacheTime
     */
    public function __construct(RestInterface $restService, CacheInterface $cacheProvider, $cacheTime = 1)
    {
        $this->decorate = $restService;
        $this->cacheProvider = $cacheProvider;
        $this->cacheTime = $cacheTime;
    }

    /**
     * {@inheritdoc}
     */
    public function get($url, $parameters = array(), $headers = array())
    {
        $cacheKey = $url . json_encode($parameters) . json_encode($headers);
        return $this->callCached('get', sha1($cacheKey), $url, $parameters = array(), $headers = array());
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $parameters = array(), $headers = array())
    {
        return $this->decorate->post($url, $parameters, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, $parameters = array(), $headers = array())
    {
        return $this->decorate->put($url, $parameters, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url, $parameters = array(), $headers = array())
    {
        return $this->decorate->delete($url, $parameters, $headers);
    }

    /**
     * @param  string $call
     * @param  string $key
     * @param  string $url
     * @param  array $parameters
     * @param  array $headers
     * @return bool|mixed
     */
    public function callCached($call, $key, $url, $parameters = array(), $headers = array())
    {
        /** @var $response ResponseInterface */
        if (!$this->cacheProvider || $this->cacheTime == 0) {
            $response = call_user_func(array($this->decorate, $call), $url, $parameters, $headers);
        } else {
            $response = $this->cacheProvider->read($key);
            if ($response === false) {
                $response = call_user_func(array($this->decorate, $call), $url, $parameters, $headers);
                if ($response === false) {
                    return false;
                }
                // Don't cache errors
                if (!$response->getErrorMessage()) {
                    $this->cacheProvider->write($key, serialize($response), $this->cacheTime);
                }
            } else {
                $response = unserialize($response);
            }
        }

        return $response;
    }
}
