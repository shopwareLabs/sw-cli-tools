<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional\PluginCreator\OutputDirectoryProvider;

use Shopware\PluginCreator\Services\WorkingDirectoryProvider\CurrentOutputDirectoryProvider;

class CurrentOutputDirectoryProviderTest extends \PHPUnit_Framework_TestCase
{
    const PLUGIN_NAME = 'SwagTest';

    public function testGetCurrentPath()
    {
        $expectedPath = getcwd() . '/custom/plugins/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->getMock('Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector');
        $shopwareRootDetectorStub->method('isRoot')
            ->willReturn(true);

        $currentOutputDirectoryProvider = new CurrentOutputDirectoryProvider($shopwareRootDetectorStub, self::PLUGIN_NAME);
        $path = $currentOutputDirectoryProvider->getPath();

        $this->assertEquals($expectedPath, $path);
    }

    public function testGetPathIfNotExecutedFromShopwareRootFolder()
    {
        $expectedPath = getcwd() . '/' . self::PLUGIN_NAME . '/';

        $shopwareRootDetectorStub = $this->getMock('Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector');
        $shopwareRootDetectorStub->method('isRoot')
            ->willReturn(false);

        $currentOutputDirectoryProvider = new CurrentOutputDirectoryProvider($shopwareRootDetectorStub, self::PLUGIN_NAME);

        $path = $currentOutputDirectoryProvider->getPath();

        $this->assertEquals($expectedPath, $path);
    }
}
