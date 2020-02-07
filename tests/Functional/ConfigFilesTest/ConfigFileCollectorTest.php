<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional\ConfigFileTest;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ShopwareCli\ConfigFileCollector;
use ShopwareCli\Services\PathProvider\PathProvider;

class ConfigFileCollectorTest extends TestCase
{
    public function testSome(): void
    {
        $testDir = __DIR__ . '/_fixtures';

        /** @var PathProvider|MockObject $pathProvider */
        $pathProvider = $this->getMockBuilder(PathProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pathProvider->expects(static::once())
            ->method('getConfigPath')
            ->willReturn(__DIR__);

        $pathProvider->expects(static::once())
            ->method('getExtensionPath')
            ->willReturn($testDir);

        $pathProvider->expects(static::once())
            ->method('getCliToolPath')
            ->willReturn($testDir);

        $result = (new ConfigFileCollector($pathProvider))->collectConfigFiles();

        $expectedResults = [
            $testDir . '/VendorA/ExtB/config.yaml',
            $testDir . '/VendorC/ExtA/config.yaml',
            $testDir . '/config.yaml.dist',
        ];

        foreach ($expectedResults as $expectedResult) {
            static::assertContains($expectedResult, $result);
        }
    }
}
