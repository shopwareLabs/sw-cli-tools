<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services;

use Shopware\Plugin\RepositoryAwareExtension;
use Shopware\Plugin\Services\Repositories\DefaultRepositoryFactory;
use ShopwareCli\Application\ExtensionManager;

/**
 * Collect default and plugin repositories
 */
class RepositoryManager
{
    /**
     * @var DefaultRepositoryFactory
     */
    private $defaultRepositoryFactory;

    /**
     * @var ExtensionManager
     */
    private $extensionManager;

    public function __construct(ExtensionManager $extensionManager, DefaultRepositoryFactory $defaultRepositoryFactory)
    {
        $this->defaultRepositoryFactory = $defaultRepositoryFactory;
        $this->extensionManager = $extensionManager;
    }

    /**
     * Return default and plugin repositories
     */
    public function getRepositories(): array
    {
        $defaultRepositories = $this->defaultRepositoryFactory->getDefaultRepositories();
        $pluginRepositories = $this->collectPluginRepositories();

        return array_merge($defaultRepositories, $pluginRepositories);
    }

    /**
     * Iterate all plugins and collect plugin repositories
     */
    private function collectPluginRepositories(): array
    {
        $repositories = [];

        foreach ($this->extensionManager->getExtensions() as $plugin) {
            if ($plugin instanceof RepositoryAwareExtension) {
                foreach ($plugin->getRepositories() as $repository) {
                    $repositories[] = $repository;
                }
            }
        }

        return $repositories;
    }
}
