<?php

namespace ShopwareCli\tests\PluginCreator\OutputDirectoryProvider;

use Shopware\PluginCreator\Services\WorkingDirectoryProvider\LegacyOutputDirectoryProvider;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector;

class LegacyOutputDirectoryProviderTest extends \PHPUnit_Framework_TestCase
{
    const PLUGIN_NAME = 'SwagTest';

    public function testGetLegacyPath()
    {
        $expectedPath = getcwd() . '/engine/Shopware/Plugins/Local/Frontend/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->getMock(ShopwareRootDetector::class);
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

        $shopwareRootDetectorStub = $this->getMock(ShopwareRootDetector::class);
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
