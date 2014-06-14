<?php

namespace ShopwareCli\Application;

use ShopwareCli\Services\DefaultRepositoryFactory;

/**
 * Collect default and plugin repositories
 *
 * Class RepositoryManager
 * @package ShopwareCli\Application
 */
class RepositoryManager
{
    /**
     * @var \ShopwareCli\Services\DefaultRepositoryFactory
     */
    private $defaultRepositoryFactory;
    /**
     * @var PluginManager
     */
    private $pluginManager;

    public function __construct(PluginManager $pluginManager, DefaultRepositoryFactory $defaultRepositoryFactory)
    {
        $this->defaultRepositoryFactory = $defaultRepositoryFactory;
        $this->pluginManager = $pluginManager;
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
        $repositories = array();

        foreach ($this->pluginManager->getPlugins() as $plugin) {
            if ($plugin instanceof RepositoryAwarePlugin) {
                foreach ($plugin->getRepositories() as $repository) {
                    $repositories[] = $repository;
                }
            }
        }
        return $repositories;
    }
}