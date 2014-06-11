<?php

namespace ShopwareCli\Cache;

interface CacheInterface
{
    public function write($key, $data, $valid);

    public function read($key);

    public function delete($key);

    public function exists($key);

    public function clear();

    public function getKeys();
}