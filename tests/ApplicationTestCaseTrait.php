<?php

namespace ShopwareCli\Tests;

use ShopwareCli\Application;

trait ApplicationTestCaseTrait
{
    /**
     * @var Application
     */
    private static $application;

    /**
     * @return Application
     */
    public static function getApplication()
    {
        if (!self::$application) {
            self::bootApplication();
        }

        return self::$application;
    }

    public static function bootApplication()
    {
        self::$application = new Application(\TestLoaderProvider::getLoader());
    }

    /**
     * @after
     */
    protected function destoryApplicationAfter()
    {
        self::$application = false;
    }
}
