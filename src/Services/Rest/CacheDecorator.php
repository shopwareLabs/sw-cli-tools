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
    protected $decorate;
    protected $cacheProvider;
    protected $cacheTime;

    public function __construct(RestInterface $restService, CacheInterface $cacheProvider, $cacheTime = 1)
    {
        $this->decorate = $restService;
        $this->cacheProvider = $cacheProvider;
        $this->cacheTime = $cacheTime;
    }

    public function get($url, $parameters = array(), $headers = array())
    {
        return $this->callCached('get', sha1($url), $url, $parameters = array(), $headers = array());
    }

    public function post($url, $parameters = array(), $headers = array())
    {
        call_user_func(array($this->decorate, 'post'), $url, $parameters, $headers);
    }

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
                $this->cacheProvider->write($key, json_encode($content), $this->cacheTime);
            } else {
                $content = json_decode($content, true);
            }
        }

        return $content;
    }
}
