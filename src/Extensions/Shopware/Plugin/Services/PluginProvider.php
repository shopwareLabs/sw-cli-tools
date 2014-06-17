<?php

namespace Shopware\Plugin\Services;

use Shopware\Plugin\Services\Repositories\BaseRepository;
use ShopwareCli\Config;
use Shopware\Plugin\Services\Repositories\RepositoryInterface;
use Shopware\Plugin\Struct\Plugin;

/**
 * Class PluginProvider
 * @package ShopwareCli\Plugin
 */
class PluginProvider
{
    /**
     * @var RepositoryInterface[]
     */
    protected $repositories = array();

    /**
     * @var string
     */
    protected $sortBy;

    /**
     * @var \ShopwareCli\Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->sortBy = $config['general']['sortBy'];
    }

    /**
     * @param $repositories
     */
    public function setRepositories($repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * Sort a given array of plugins by the configured properties
     *
     * @param $plugins
     * @return Plugin[]
     */
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

    /**
     * Query all plugin repositories for plugins named like $name
     *
     * @param  string   $name string  Name to search for
     * @param $exact    boolean Whether to search for exact match or not
     * @return Plugin[]
     */
    public function getPluginByName($name, $exact = false)
    {
        $result = array();
        foreach ($this->repositories as $repo) {
            $result = array_merge($result, $repo->getPluginByName($name, $exact));
        }

        return $this->sortPlugins($result);
    }

    /**
     * Query plugin repositories by $name and return the plugins contained in it
     *
     * @param  string   $name string  Repo name to search for
     * @return Plugin[]
     */
    public function getPluginsByRepositoryName($name)
    {
        $result = array();
        foreach ($this->repositories as $repo) {
            if ($repo instanceof BaseRepository && stripos($repo->getName(), $name) !== false) {
                $result = array_merge($result, $repo->getPlugins());
            }
        }

        return $this->sortPlugins($result);
    }

    /**
     * Query all plugin repositories for all available plugins
     *
     * @return Plugin[]
     */
    public function getPlugins()
    {
        $result = array();
        foreach ($this->repositories as $repo) {
            $result = array_merge($result, $repo->getPlugins());
        }

        return $this->sortPlugins($result);
    }
}
