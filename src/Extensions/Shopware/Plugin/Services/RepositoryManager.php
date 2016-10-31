<?php

namespace Shopware\Plugin\Services;

use Shopware\Plugin\Services\Repositories\DefaultRepositoryFactory;
use ShopwareCli\Application\ExtensionManager;
use Shopware\Plugin\RepositoryAwareExtension;

/**
 * Collect default and plugin repositories
 *
 * Class RepositoryManager
 */
class RepositoryManager
{
    /**
     * @var \Shopware\Plugin\Services\Repositories\DefaultRepositoryFactory
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
     *
     * @return array
     */
    public function getRepositories()
    {
        $defaultRepositories = $this->defaultRepositoryFactory->getDefaultRepositories();
        $pluginRepositories = $this->collectPluginRepositories();

        return array_merge($defaultRepositories, $pluginRepositories);
    }

    /**
     * Iterate all plugins and collect plugin repositories
     *
     * @return array
     */
    private function collectPluginRepositories()
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
