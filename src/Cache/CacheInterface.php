<?php

namespace ShopwareCli\Cache;

interface CacheInterface
{
    /**
     * @param string  $key
     * @param string  $data
     * @param int $valid
     *
     * @return bool
     */
    public function write($key, $data, $valid);

    /**
     * @param string $key
     *
     * @return string|false
     */
    public function read($key);

    /**
     * @return void
     */
    public function delete($key);

    /**
     * @return bool
     */
    public function exists($key);

    /**
     * @return void
     */
    public function clear();

    public function getKeys();
}
