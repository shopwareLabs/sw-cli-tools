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
        foreach ($this->getShopwareDirectories() as $directory) {
            rmdir(self::TEST_ROOT . $directory);
        }

        foreach ($this->getShopwareFiles() as $file) {
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
        foreach ($this->getShopwareDirectories() as $directory) {
            mkdir(self::TEST_ROOT . $directory);
        }

        foreach ($this->getShopwareFiles() as $file) {
            file_put_contents(self::TEST_ROOT . $file, 'test');
        }
    }

    /**
     * @return array
     */
    private function getShopwareDirectories()
    {
        return [
            '/engine',
            '/var',
            '/bin',
            '/vendor',
            '/files'
        ];
    }

    /**
     * @return array
     */
    private function getShopwareFiles()
    {
        return [
            '/shopware.php'
        ];
    }
}
