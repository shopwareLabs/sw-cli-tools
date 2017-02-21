<?php

final class TestLoaderProvider
{
    /**
     * @var \Composer\Autoload\ClassLoader
     */
    private static $loader;

    private function __construct()
    {
    }

    public static function setLoader(\Composer\Autoload\ClassLoader $loader)
    {
        self::$loader = $loader;
    }

    public static function getLoader()
    {
        return self::$loader;
    }
}

$loader = require __DIR__ . '/vendor/autoload.php';
TestLoaderProvider::setLoader($loader);
