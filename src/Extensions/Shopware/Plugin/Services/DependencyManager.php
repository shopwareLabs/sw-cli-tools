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
        $this->processExecutor->execute('composer install --no-dev', $pathToPlugin);

        if (!\file_exists(\sprintf('%s/vendor/shopware', $pathToPlugin))) {
            return;
        }

        $this->processExecutor->execute('rm -rf vendor/shopware', $pathToPlugin);
    }
}
