<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional\ConfigFileTest;

use ShopwareCli\ConfigFileCollector;

/**
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class ConfigFileCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testSome()
    {
        $testDir = __DIR__ . '/_fixtures';

        $pathProvider = $this->getMockBuilder('ShopwareCli\Services\PathProvider\PathProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $pathProvider->expects($this->once())
            ->method('getConfigPath')
            ->will($this->returnValue(__DIR__));

        $pathProvider->expects($this->once())
            ->method('getExtensionPath')
            ->will($this->returnValue($testDir));

        $pathProvider->expects($this->once())
            ->method('getCliToolPath')
            ->will($this->returnValue($testDir));

        $SUT = new ConfigFileCollector($pathProvider);
        $result = $SUT->collectConfigFiles();

        $expectedResults = [
            $testDir . '/VendorA/ExtB/config.yaml',
            $testDir . '/VendorC/ExtA/config.yaml',
            $testDir . '/config.yaml.dist',
        ];

        foreach ($expectedResults as $expectedResult) {
            $this->assertContains($expectedResult, $result);
        }
    }
}
