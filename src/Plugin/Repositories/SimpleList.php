<?php

namespace ShopwareCli\Plugin\Repositories;

use ShopwareCli\Plugin\BaseRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
                $cloneUrl = $this->useHttp ? $cloneUrls['http'] : $cloneUrls['ssh'];
                $plugins[] = $this->createPlugin($cloneUrl, $name);
            }
        }

        return $plugins;
    }
}
