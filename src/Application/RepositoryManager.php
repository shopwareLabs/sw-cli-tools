<?php

namespace ShopwareCli\Application;

use ShopwareCli\Plugin\DefaultRepositoryFactory;

class RepositoryManager
{
    /**
     * @var \ShopwareCli\Plugin\DefaultRepositoryFactory
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

    public function getRepositories()
    {
        $repositories = $this->defaultRepositoryFactory->getDefaultRepositories();

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