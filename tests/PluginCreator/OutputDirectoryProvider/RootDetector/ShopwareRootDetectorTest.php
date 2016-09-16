<?php

use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector;

class ShopwareRootDetectorTest extends \PHPUnit_Framework_TestCase
{
    const TEST_ROOT = __DIR__ . '/testroot';

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
            rmdir(self::TEST_ROOT . $directory);
        }

        foreach (ShopwareRootDetector::FILES as $file) {
            unlink(self::TEST_ROOT . $file);
        }
        rmdir(self::TEST_ROOT);
    }

    public function testPathIsShopwareRoot()
    {
        $return = $this->SUT->isRoot(self::TEST_ROOT);
        $this->assertTrue($return);
    }

    public function testPathIsNotShopwareRoot()
    {
        $return = $this->SUT->isRoot('/home/not_shopware');
        $this->assertFalse($return);
    }

    private function createRootFolder()
    {
        mkdir(self::TEST_ROOT);
        foreach (ShopwareRootDetector::DIRECTORIES as $directory) {
            mkdir(self::TEST_ROOT . $directory);
        }

        foreach (ShopwareRootDetector::FILES as $file) {
            file_put_contents(self::TEST_ROOT . $file, 'test');
        }
    }
}
