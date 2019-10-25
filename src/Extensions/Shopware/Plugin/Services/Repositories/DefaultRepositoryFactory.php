<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services\Repositories;

use ShopwareCli\Config;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Step through the config file, lookup needed repositories and create them
 *
 * Class DefaultRepositoryFactory
 */
class DefaultRepositoryFactory
{
    /**
     * List of repositories the DefaultRepository will handle
     *
     * @var array
     */
    private $supportedRepositories = ['GitHub', 'Stash', 'BitBucket', 'SimpleList', 'GitLab'];

    private $defaultRepositories = [];

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
        if (!$this->defaultRepositories) {
            $this->setupRepositories();
        }

        return $this->defaultRepositories;
    }

    /**
     * Iterate all repositories in the config and set them up
     */
    private function setupRepositories()
    {
        /** @var Config $config */
        $config = $this->container->get('config');

        foreach ($config->getRepositories() as $type => $data) {
            if (!in_array($type, $this->supportedRepositories)) {
                continue;
            }

            $this->createSubRepositories($type, $data);
        }
    }

    /**
     * Setup all sub-repositories
     *
     * @param $type
     * @param $data
     */
    private function createSubRepositories($type, $data)
    {
        $baseUrl = isset($data['config']['endpoint']) ? $data['config']['endpoint'] : null;
        $username = isset($data['config']['username']) ? $data['config']['username'] : null;
        $password = isset($data['config']['password']) ? $data['config']['password'] : null;

        foreach ($data['repositories'] as $name => $repoConfig) {
            $cacheTime = isset($repoConfig['cache']) ? $repoConfig['cache'] : 3600;

            $restClient = $baseUrl ? $this->container->get('rest_service_factory')->factory($baseUrl, $username, $password, $cacheTime) : null;

            $this->defaultRepositories[] = $this->createRepository($name, $type, $repoConfig, $restClient);
        }
    }

    /**
     * @param $name
     * @param $type
     * @param $repoConfig
     * @param $restClient
     *
     * @return BaseRepository
     */
    private function createRepository($name, $type, $repoConfig, $restClient)
    {
        $className = 'Shopware\\Plugin\\Services\\Repositories\\Standard\\' . $type;
        $repo = new $className(
            isset($repoConfig['url']) ? $repoConfig['url'] : '',
            $name,
            $restClient
        );

        if ($repo instanceof ContainerAwareInterface) {
            $repo->setContainer($this->container);
        }

        return $repo;
    }
}
