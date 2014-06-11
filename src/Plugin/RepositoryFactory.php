<?php

namespace ShopwareCli\Plugin;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class RepositoryFactory
 * @package ShopwareCli\Plugin
 */
class RepositoryFactory
{

    /** @var \Symfony\Component\DependencyInjection\Container  */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected $repos = array();
    protected $sortBy;

    public function factory($useHttp)
    {
        $config = $this->container->get('config');
        $this->sortBy = $config['general']['sortBy'];

        foreach ($config->getRepositories() as $type => $data) {
            $className = 'ShopwareCli\\Plugin\\Repositories\\' . $type;

            $options = array();
            if (isset($data['config'])) {
                $options = array(
                    'base_url' => $data['config']['endpoint'],
                    'username' => $data['config']['username'],
                    'password' => $data['config']['password']
                );
            }

            foreach ($data['repositories'] as $name => $repoConfig) {
                $cacheTime = isset($repoConfig['cache']) ? $repoConfig['cache'] : 3600;

                $repo = new $className(
                    isset($repoConfig['url']) ? $repoConfig['url'] : '',
                    $useHttp,
                    $name,
                    $this->container->get('rest_service_factory')->factory($options, $cacheTime)
                );
                $this->repos[] = $repo;

                if ($repo instanceof ContainerAwareInterface) {
                    $repo->setContainer($this->container);
                }
            }

        }

        return $this;
    }

    protected function sortPlugins($plugins)
    {
        switch ($this->sortBy) {
            case 'repository':
                usort($plugins, function ($a, $b) {
                    return $a->repository . $a->name > $b->repository . $b->name;
                });
                break;
            case 'module':
                usort($plugins, function ($a, $b) {
                    return $a->module . $a->name > $b->module . $b->name;
                });
                break;
            default:
                usort($plugins, function ($a, $b) {
                    return $a->name > $b->name;
                });
                break;
        }

        return $plugins;
    }

    public function getPluginByName($name)
    {
        $result = array();

        /** @var $repo Repository */
        foreach ($this->repos as $repo) {
            $result = array_merge($result, $repo->getPluginByName($name));
        }

        return $this->sortPlugins($result);
    }

    public function getPlugins()
    {
        $result = array();

        /** @var $repo Repository */
        foreach ($this->repos as $repo) {
            $result = array_merge($result, $repo->getPlugins());
        }

        return $this->sortPlugins($result);
    }
}