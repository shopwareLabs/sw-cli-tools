<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional\PluginCreator\OutputDirectoryProvider;

use PHPUnit\Framework\TestCase;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\LegacyOutputDirectoryProvider;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector;

class LegacyOutputDirectoryProviderTest extends TestCase
{
    private const PLUGIN_NAME = 'SwagTest';

    public function testGetLegacyPath(): void
    {
        $expectedPath = \getcwd() . '/engine/Shopware/Plugins/Local/Frontend/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->createMock(ShopwareRootDetector::class);
        $shopwareRootDetectorStub->method('isRoot')
            ->willReturn(true);

        $legacyOutputDirectoryProvider = new LegacyOutputDirectoryProvider(
            $shopwareRootDetectorStub,
            self::PLUGIN_NAME,
            LegacyOutputDirectoryProvider::FRONTEND_NAMESPACE
        );

        $path = $legacyOutputDirectoryProvider->getPath();

        static::assertEquals($expectedPath, $path);
    }

    public function testGetLegacyPathIfNotExecutedFromShopwareRoot(): void
    {
        $expectedPath = \getcwd() . '/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->createMock(ShopwareRootDetector::class);
        $shopwareRootDetectorStub->method('isRoot')
            ->willReturn(false);

        $legacyOutputDirectoryProvider = new LegacyOutputDirectoryProvider(
            $shopwareRootDetectorStub,
            self::PLUGIN_NAME,
            LegacyOutputDirectoryProvider::FRONTEND_NAMESPACE
        );

        $path = $legacyOutputDirectoryProvider->getPath();

        static::assertEquals($expectedPath, $path);
    }
}
