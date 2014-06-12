<?php

namespace ShopwareCli\Plugin;

use ShopwareCli\Struct\Plugin;

/**
 * Interface for repository classes
 *
 * Class RepositoryInterface
 * @package ShopwareCli\Plugin
 */
interface RepositoryInterface
{
    /**
     * @param $name
     * @return Plugin[]
     */
    public function getPluginByName($name);

    /**
     * @return Plugin[]
     */
    public function getPlugins();
}
