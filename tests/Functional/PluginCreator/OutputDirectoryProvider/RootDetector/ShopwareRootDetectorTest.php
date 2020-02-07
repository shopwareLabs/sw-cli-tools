<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional\PluginCreator\OutputDirectoryProvider\RootDetector;

use PHPUnit\Framework\TestCase;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector;

class ShopwareRootDetectorTest extends TestCase
{
    /**
     * @var ShopwareRootDetector
     */
    private $SUT;

    protected function setUp(): void
    {
        $this->createRootFolder();
        $this->SUT = new ShopwareRootDetector();
    }

    protected function tearDown(): void
    {
        foreach (ShopwareRootDetector::getDirectories() as $directory) {
            rmdir(self::getTestRoot() . $directory);
        }

        foreach (ShopwareRootDetector::getFiles() as $file) {
            unlink(self::getTestRoot() . $file);
        }
        rmdir(self::getTestRoot());
    }

    public function testPathIsShopwareRoot(): void
    {
        $return = $this->SUT->isRoot(self::getTestRoot());
        static::assertTrue($return);
    }

    public function testPathIsNotShopwareRoot(): void
    {
        $return = $this->SUT->isRoot('/home/not_shopware');
        static::assertFalse($return);
    }

    private static function getTestRoot(): string
    {
        return __DIR__ . '/testroot';
    }

    private function createRootFolder(): void
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
