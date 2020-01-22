<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Composer\Autoload\ClassLoader;

final class TestLoaderProvider
{
    /**
     * @var ClassLoader
     */
    private static $loader;

    private function __construct()
    {
    }

    public static function setLoader(ClassLoader $loader): void
    {
        self::$loader = $loader;
    }

    public static function getLoader(): ClassLoader
    {
        return self::$loader;
    }
}

$loader = require __DIR__ . '/vendor/autoload.php';
TestLoaderProvider::setLoader($loader);
