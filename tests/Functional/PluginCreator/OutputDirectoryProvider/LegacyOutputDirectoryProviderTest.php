<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional\PluginCreator\OutputDirectoryProvider;

use Shopware\PluginCreator\Services\WorkingDirectoryProvider\LegacyOutputDirectoryProvider;

class LegacyOutputDirectoryProviderTest extends \PHPUnit_Framework_TestCase
{
    const PLUGIN_NAME = 'SwagTest';

    public function testGetLegacyPath()
    {
        $expectedPath = getcwd() . '/engine/Shopware/Plugins/Local/Frontend/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->getMock('Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector');
        $shopwareRootDetectorStub->method('isRoot')
            ->willReturn(true);

        $legacyOutputDirectoryProvider = new LegacyOutputDirectoryProvider(
            $shopwareRootDetectorStub,
            self::PLUGIN_NAME,
            LegacyOutputDirectoryProvider::FRONTEND_NAMESPACE
        );

        $path = $legacyOutputDirectoryProvider->getPath();

        $this->assertEquals($expectedPath, $path);
    }

    public function testGetLegacyPathIfNotExecutedFromShopwareRoot()
    {
        $expectedPath = getcwd() . '/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->getMock('Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector');
        $shopwareRootDetectorStub->method('isRoot')
            ->willReturn(false);

        $legacyOutputDirectoryProvider = new LegacyOutputDirectoryProvider(
            $shopwareRootDetectorStub,
            self::PLUGIN_NAME,
            LegacyOutputDirectoryProvider::FRONTEND_NAMESPACE
        );

        $path = $legacyOutputDirectoryProvider->getPath();

        $this->assertEquals($expectedPath, $path);
    }
}
