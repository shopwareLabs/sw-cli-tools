<?php

namespace ShopwareCli\Plugin;

/**
 * Interface for repository classes
 *
 * Class RepositoryInterface
 * @package ShopwareCli\Plugin
 */
interface RepositoryInterface
{
    public function getPluginByName($name);

    public function getPlugins();
}