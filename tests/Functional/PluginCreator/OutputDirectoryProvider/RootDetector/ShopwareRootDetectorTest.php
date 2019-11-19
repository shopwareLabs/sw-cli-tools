<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector;

class ShopwareRootDetectorTest extends \PHPUnit_Framework_TestCase
{
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
        foreach (ShopwareRootDetector::getDirectories() as $directory) {
            rmdir(self::getTestRoot() . $directory);
        }

        foreach (ShopwareRootDetector::getFiles() as $file) {
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

    private static function getTestRoot()
    {
        return __DIR__ . '/testroot';
    }

    private function createRootFolder()
    {
        mkdir(self::getTestRoot());
        foreach (ShopwareRootDetector::getDirectories() as $directory) {
            mkdir(self::getTestRoot() . $directory);
        }

        foreach (ShopwareRootDetector::getFiles() as $file) {
            file_put_contents(self::getTestRoot() . $file, 'test');
        }
    }
}
