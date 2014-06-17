<?php

namespace Shopware\Plugin\Services\Repositories;

use Shopware\Plugin\Struct\Plugin;

/**
 * Interface for repository classes
 *
 * Class RepositoryInterface
 * @package ShopwareCli\Plugin
 */
interface RepositoryInterface
{
    /**
     * Return available plugins named $name.
     * If $exact is true, search should be exact (==), else  stripos() or similar
     *
     * @param string $name
     * @param bool   $exact
     *
     * @return Plugin[]
     */
    public function getPluginByName($name, $exact = false);

    /**
     * Return all known plugins
     *
     * @return Plugin[]
     */
    public function getPlugins();
}
