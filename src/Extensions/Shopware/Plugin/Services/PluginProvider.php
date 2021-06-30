<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services;

use Shopware\Plugin\Services\Repositories\BaseRepository;
use Shopware\Plugin\Services\Repositories\RepositoryInterface;
use Shopware\Plugin\Struct\Plugin;
use ShopwareCli\Config;

class PluginProvider
{
    /**
     * @var RepositoryInterface[]
     */
    protected $repositories = [];

    /**
     * @var string
     */
    protected $sortBy;

    public function __construct(Config $config)
    {
        $this->sortBy = $config['general']['sortBy'];
    }

    public function setRepositories($repositories): void
    {
        $this->repositories = $repositories;
    }

    /**
     * Query all plugin repositories for plugins named like $name
     *
     * @param string $name  string  Name to search for
     * @param mixed  $exact boolean Whether to search for exact match or not
     *
     * @return Plugin[]
     */
    public function getPluginByName($name, $exact = false)
    {
        $result = [];
        foreach ($this->repositories as $repo) {
            $result = \array_merge($result, $repo->getPluginByName($name, $exact));
        }

        return $this->sortPlugins($result);
    }

    /**
     * Query plugin repositories by $name and return the plugins contained in it
     *
     * @param string $name string  Repo name to search for
     *
     * @return Plugin[]
     */
    public function getPluginsByRepositoryName($name): array
    {
        $result = [];
        foreach ($this->repositories as $repo) {
            if ($repo instanceof BaseRepository && \stripos($repo->getName(), $name) !== false) {
                $result = \array_merge($result, $repo->getPlugins());
            }
        }

        return $this->sortPlugins($result);
    }

    /**
     * Query all plugin repositories for all available plugins
     *
     * @return Plugin[]
     */
    public function getPlugins(): array
    {
        $result = [];
        foreach ($this->repositories as $repo) {
            $result = \array_merge($result, $repo->getPlugins());
        }

        return $this->sortPlugins($result);
    }

    /**
     * Sort a given array of plugins by the configured properties
     *
     * @return Plugin[]
     */
    protected function sortPlugins($plugins): array
    {
        switch ($this->sortBy) {
            case 'repository':
                \usort($plugins, static function ($a, $b) {
                    return $a->repository . $a->name > $b->repository . $b->name;
                });
                break;
            case 'module':
                \usort($plugins, static function ($a, $b) {
                    return $a->module . $a->name > $b->module . $b->name;
                });
                break;
            default:
                \usort($plugins, static function ($a, $b) {
                    return $a->name > $b->name;
                });
                break;
        }

        return $plugins;
    }
}
