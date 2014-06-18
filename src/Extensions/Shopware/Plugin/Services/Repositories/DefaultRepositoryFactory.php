<?php

namespace Shopware\Plugin\Services\Repositories;

use ShopwareCli\Config;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Step through the config file, lookup needed repositories and create them
 *
 * Class DefaultRepositoryFactory
 * @package Shopware\Plugin\Services\Repositories
 */
class DefaultRepositoryFactory
{

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return RepositoryInterface[]
     */
    public function getDefaultRepositories()
    {
        /** @var Config $config */
        $config = $this->container->get('config');

        $repositories = array();

        foreach ($config->getRepositories() as $type => $data) {
            $className = 'Shopware\\Plugin\\Services\\Repositories\\Standard\\' . $type;

            $baseUrl = isset($data['config']['endpoint']) ? $data['config']['endpoint'] : null;
            $username = isset($data['config']['username']) ? $data['config']['username'] : null;
            $password = isset($data['config']['password']) ? $data['config']['password'] : null;

            foreach ($data['repositories'] as $name => $repoConfig) {
                $cacheTime = isset($repoConfig['cache']) ? $repoConfig['cache'] : 3600;

                $restClient = null;
                if ($baseUrl) {
                    $restClient = $this->container->get('rest_service_factory')->factory($baseUrl, $username, $password, $cacheTime);
                }

                $repo = new $className(
                    isset($repoConfig['url']) ? $repoConfig['url'] : '',
                    $name,
                    $restClient
                );
                $repositories[] = $repo;

                if ($repo instanceof ContainerAwareInterface) {
                    $repo->setContainer($this->container);
                }
            }
        }

        return $repositories;
    }
}
