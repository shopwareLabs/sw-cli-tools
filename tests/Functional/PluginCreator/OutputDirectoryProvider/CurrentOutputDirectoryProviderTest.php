<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional\PluginCreator\OutputDirectoryProvider;

use PHPUnit\Framework\TestCase;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\CurrentOutputDirectoryProvider;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector;

class CurrentOutputDirectoryProviderTest extends TestCase
{
    private const PLUGIN_NAME = 'SwagTest';

    public function testGetCurrentPath(): void
    {
        $expectedPath = getcwd() . '/custom/plugins/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->createMock(ShopwareRootDetector::class);
        $shopwareRootDetectorStub->method('isRoot')
            ->willReturn(true);

        $currentOutputDirectoryProvider = new CurrentOutputDirectoryProvider($shopwareRootDetectorStub, self::PLUGIN_NAME);
        $path = $currentOutputDirectoryProvider->getPath();

        static::assertEquals($expectedPath, $path);
    }

    public function testGetPathIfNotExecutedFromShopwareRootFolder(): void
    {
        $expectedPath = getcwd() . '/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->createMock(ShopwareRootDetector::class);
        $shopwareRootDetectorStub->method('isRoot')
            ->willReturn(false);

        $currentOutputDirectoryProvider = new CurrentOutputDirectoryProvider($shopwareRootDetectorStub, self::PLUGIN_NAME);

        $path = $currentOutputDirectoryProvider->getPath();

        static::assertEquals($expectedPath, $path);
    }
}
