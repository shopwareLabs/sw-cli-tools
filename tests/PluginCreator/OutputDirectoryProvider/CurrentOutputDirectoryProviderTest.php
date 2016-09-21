<?php

namespace ShopwareCli\tests\PluginCreator\OutputDirectoryProvider;

use Shopware\PluginCreator\Services\WorkingDirectoryProvider\CurrentOutputDirectoryProvider;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector;

class CurrentOutputDirectoryProviderTest extends \PHPUnit_Framework_TestCase
{
    const PLUGIN_NAME = 'SwagTest';

    public function testGetCurrentPath()
    {
        $expectedPath = getcwd() . '/custom/plugins/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->getMock(ShopwareRootDetector::class);
        $shopwareRootDetectorStub->method('isRoot')
            ->willReturn(true);

        $currentOutputDirectoryProvider = new CurrentOutputDirectoryProvider($shopwareRootDetectorStub, self::PLUGIN_NAME);
        $path = $currentOutputDirectoryProvider->getPath();

        $this->assertEquals($expectedPath, $path);
    }


    public function testGetPathIfNotExecutedFromShopwareRootFolder()
    {
        $expectedPath = getcwd() . '/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->getMock(ShopwareRootDetector::class);
        $shopwareRootDetectorStub->method('isRoot')
            ->willReturn(false);

        $currentOutputDirectoryProvider = new CurrentOutputDirectoryProvider($shopwareRootDetectorStub, self::PLUGIN_NAME);

        $path = $currentOutputDirectoryProvider->getPath();

        $this->assertEquals($expectedPath, $path);
    }
}
