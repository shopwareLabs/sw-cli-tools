<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Extensions\Shopware\Plugin\Services;

use Shopware\Plugin\Struct\Plugin;
use ShopwareCli\Services\ProcessExecutor;

class DependencyManager
{
    /**
     * @var ProcessExecutor
     */
    private $processExecutor;

    public function __construct(ProcessExecutor $processExecutor)
    {
        $this->processExecutor = $processExecutor;
    }

    /**
     * Will check for necessary dependencies and install them
     */
    public function manageDependencies(Plugin $plugin, string $pathToPlugin): void
    {
        if (!\file_exists($pathToPlugin . '/composer.json')) {
            return;
        }

        if (!$plugin->isShopware6) {
            $this->manageShopware5Dependencies($pathToPlugin);

            return;
        }

        $this->manageShopware6Dependencies($pathToPlugin);
    }

    private function manageShopware5Dependencies(string $pathToPlugin): void
    {
        $this->processExecutor->execute('composer install --no-dev', $pathToPlugin);
    }

    private function manageShopware6Dependencies(string $pathToPlugin): void
    {
        $composerJson = \json_decode(\file_get_contents(\sprintf('%s/composer.json', $pathToPlugin)), true);
        if (!\array_key_exists('require', $composerJson)
            || !\is_array($composerJson['require'])
            || \count($composerJson['require']) <= 0
        ) {
            return;
        }

        $shopwareDependencies = [];
        foreach ($composerJson['require'] as $dependencyName => $version) {
            if (\strpos($dependencyName, 'shopware/') === 0
                || \strpos($dependencyName, 'swag/') === 0
            ) {
                $shopwareDependencies[$dependencyName] = $version;
            }
        }

        // Only Shopware dependencies skip
        if (\count($shopwareDependencies) === \count($composerJson['require'])) {
            return;
        }

        // Temporary remove shopware dependencies
        foreach ($this->getTheRightRemovingOrder(\array_keys($shopwareDependencies)) as $dependencyName) {
            $this->processExecutor->execute(\sprintf('composer remove %s --update-no-dev', $dependencyName), $pathToPlugin);
        }

        $this->processExecutor->execute('composer install --no-dev', $pathToPlugin);

        // Re-add shopware dependencies
        foreach ($shopwareDependencies as $dependencyName => $version) {
            $this->processExecutor->execute(\sprintf('composer require %s:"%s" --no-update', $dependencyName, $version), $pathToPlugin);
        }
    }

    private function getTheRightRemovingOrder(array $dependencies): array
    {
        usort($dependencies, function (string $a, string $b): int {
            switch ('shopware/core') {
                case $a:
                    return 1;
                case $b:
                    return -1;
                default:
                    return 0;
            }
        });

        return $dependencies;
    }
}
