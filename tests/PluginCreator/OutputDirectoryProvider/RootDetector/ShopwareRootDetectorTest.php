<?php

use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector;

class ShopwareRootDetectorTest extends \PHPUnit_Framework_TestCase
{
    private static function getTestRoot()
    {
        return __DIR__ . '/testroot';
    }

    /**
     * @var ShopwareRootDetector
     */
    private $SUT;

    protected function setUp()
    {
        $this->createRootFolder();
        $this->SUT = new ShopwareRootDetector();
    }

    protected function tearDown()
    {
        foreach (ShopwareRootDetector::DIRECTORIES as $directory) {
            rmdir(self::getTestRoot() . $directory);
        }

        foreach (ShopwareRootDetector::FILES as $file) {
            unlink(self::getTestRoot() . $file);
        }
        rmdir(self::getTestRoot());
    }

    public function testPathIsShopwareRoot()
    {
        $return = $this->SUT->isRoot(self::getTestRoot());
        $this->assertTrue($return);
    }

    public function testPathIsNotShopwareRoot()
    {
        $return = $this->SUT->isRoot('/home/not_shopware');
        $this->assertFalse($return);
    }

    private function createRootFolder()
    {
        mkdir(self::getTestRoot());
        foreach (ShopwareRootDetector::DIRECTORIES as $directory) {
            mkdir(self::getTestRoot() . $directory);
        }

        foreach (ShopwareRootDetector::FILES as $file) {
            file_put_contents(self::getTestRoot() . $file, 'test');
        }
    }
}
