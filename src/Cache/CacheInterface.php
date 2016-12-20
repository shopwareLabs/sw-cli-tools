<?php

namespace ShopwareCli\Cache;

interface CacheInterface
{
    /**
     * @param string  $key
     * @param string  $data
     * @param integer $valid
     *
     * @return boolean
     */
    public function write($key, $data, $valid);

    /**
     * @param string $key
     *
     * @return string|false
     */
    public function read($key);

    
    public function delete($key);

    /**
     * @param $key
     * @return boolean
     */
    public function exists($key);

    
    public function clear();

    public function getKeys();
}
