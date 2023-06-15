<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services\Repositories;

use ShopwareCli\Config;
use ShopwareCli\Services\Rest\RestInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Step through the config file, lookup needed repositories and create them
 */
class DefaultRepositoryFactory
{
    /**
     * List of repositories the DefaultRepository will handle
     */
    private array $supportedRepositories = ['GitHub', 'Stash', 'BitBucket', 'SimpleList', 'GitLab'];

    private array $defaultRepositories = [];

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return RepositoryInterface[]
     */
    public function getDefaultRepositories(): array
    {
        if (!$this->defaultRepositories) {
            $this->setupRepositories();
        }

        return $this->defaultRepositories;
    }

    /**
     * Iterate all repositories in the config and set them up
     */
    private function setupRepositories(): void
    {
        /** @var Config $config */
        $config = $this->container->get('config');

        foreach ($config->getRepositories() as $type => $data) {
            if (!\in_array($type, $this->supportedRepositories, true)) {
                continue;
            }

            $this->createSubRepositories($type, $data);
        }
    }

    /**
     * Setup all sub-repositories
     */
    private function createSubRepositories(string $type, ?array $data): void
    {
        $baseUrl = $data['config']['endpoint'] ?? null;
        $username = $data['config']['username'] ?? null;
        $password = $data['config']['password'] ?? null;

        if (!isset($data['repositories'])) {
            return;
        }

        foreach ($data['repositories'] as $name => $repoConfig) {
            $cacheTime = $repoConfig['cache'] ?? 3600;

            $restClient = $baseUrl ? $this->container->get('rest_service_factory')->factory($baseUrl, $username, $password, $cacheTime) : null;

            $this->defaultRepositories[] = $this->createRepository($name, $type, $repoConfig, $restClient);
        }
    }

    private function createRepository(
        string $name,
        string $type,
        array $repoConfig,
        ?RestInterface $restClient
    ): BaseRepository {
        $className = 'Shopware\\Plugin\\Services\\Repositories\\Standard\\' . $type;
        /** @var BaseRepository $repo */
        $repo = new $className(
            $repoConfig['url'] ?? '',
            $name,
            $restClient
        );

        if ($repo instanceof ContainerAwareInterface) {
            $repo->setContainer($this->container);
        }

        return $repo;
    }
}
