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
        return $this->callCached('get', sha1($url), $url, $parameters = array(), $headers = array());
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
     * @param $call
     * @param $key
     * @param $url
     * @param array $parameters
     * @param array $headers
     * @return bool|mixed
     */
    public function callCached($call, $key, $url, $parameters = array(), $headers = array())
    {
        if (!$this->cacheProvider || $this->cacheTime == 0) {
            $content = call_user_func(array($this->decorate, $call), $url, $parameters, $headers);
        } else {
            $content = $this->cacheProvider->read($key);
            if ($content === false) {
                $content = call_user_func(array($this->decorate, $call), $url, $parameters, $headers);
                if ($content === false) {
                    return false;
                }
                $this->cacheProvider->write($key, serialize($content), $this->cacheTime);
            } else {
                $content = unserialize($content);
            }
        }

        return $content;
    }
}
