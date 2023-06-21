<?php
declare(strict_types=1);
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Plugin\Struct\Plugin;
use ShopwareCli\Extensions\Shopware\Plugin\Services\DependencyManager;
use ShopwareCli\Services\ProcessExecutor;

class DependencyManagerTest extends TestCase
{
    /**
     * @var ProcessExecutor&MockObject
     */
    private $processExecutor;

    private $dependencyManager;

    public function setUp(): void
    {
        $this->processExecutor = $this->createMock(ProcessExecutor::class);
        $this->dependencyManager = new DependencyManager($this->processExecutor);
    }

    public function testManageDependenciesWithExternalDependenciesWillRemuveShopwareDependenciesInRightOrder(): void
    {
        $plugin = new Plugin();
        $plugin->isShopware6 = true;
        $callNumber = 0;

        $this->processExecutor
            ->method('execute')
            ->with(static::callback(function ($command) use (&$callNumber) {
                if ($callNumber == 2) {
                    self::assertEquals('composer remove shopware/core --update-no-dev', $command, 'Removing shopware/core should be the last command in removing the packages commands list');
                }
                ++$callNumber;

                return true;
            }));
        $this->dependencyManager->manageDependencies($plugin, __DIR__ . '/../../../../_fixtures/DependencyManager/RemoveShopwareDependencyOrder');
    }
}
