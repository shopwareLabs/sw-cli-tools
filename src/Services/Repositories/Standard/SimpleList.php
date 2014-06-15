<?php

namespace ShopwareCli\Services\Repositories\Standard;

use ShopwareCli\Services\Repositories\BaseRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The simple list provides a "virtual repository" from multiple git repos which also might come from different
 * servers (e.g. local, github, bitbucket, stash…)
 *
 * Class SimpleList
 * @package ShopwareCli\Services\Repositories
 */
class SimpleList extends BaseRepository implements ContainerAwareInterface
{
    /** @var  ContainerInterface */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getPluginByName($name)
    {
        $plugins = $this->getPlugins();
        foreach ($plugins as $key => $plugin) {
            if (stripos($plugin->name, $name) === false) {
                unset ($plugins[$key]);
            }
        }

        return $plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        $config = $this->container->get('config');
        if (!isset($config['repositories']['SimpleList'])) {
            return array();
        }

        $plugins = array();
        foreach ($config['repositories']['SimpleList']['repositories'] as $repositoryName => $repository) {
            foreach ($repository['plugins'] as $name => $cloneUrls) {
                $plugins[] = $this->createPlugin($cloneUrls['ssh'], $cloneUrls['http'], $name);
            }
        }

        return $plugins;
    }
}
