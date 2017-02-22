<?php
/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace ShopwareCli\Tests\Functional\ConfigFileTest;

use ShopwareCli\ConfigFileCollector;

/**
 * @category  Shopware
 * @package   ShopwareCli\Tests\Functional
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
