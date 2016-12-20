<?php

namespace Shopware\Plugin\Services;

use Shopware\Plugin\Struct\Plugin;

/**
 * Create a plugin struct from the passed data
 *
 * Class PluginFactory
 * @package ShopwareCli\Plugin
 */
class PluginFactory
{
    /**
     * Input is a name like Frontend_SwagBusinessEssentials
     *
     * @param  string $name
     * @param  string $sshUrl
     * @param  string $httpUrl
     * @param  string $repoName
     * @param $repoType
     * @return Plugin
     */
    public static function getPlugin($name, $sshUrl, $httpUrl, $repoName, $repoType = null)
    {
        $plugin = new Plugin();

        self::setPluginModuleFromName($name, $plugin);

        $plugin->cloneUrlSsh = $sshUrl;
        $plugin->cloneUrlHttp = $httpUrl;
        $plugin->repository = $repoName;
        $plugin->repoType = $repoType;

        return $plugin;
    }

    /**
     * @param string $name
     * @param Plugin $plugin
     */
    private static function setPluginModuleFromName($name, Plugin $plugin)
    {
        if (stripos($name, 'frontend') === 0) {
            $plugin->module = 'Frontend';
            $plugin->name = substr($name, 9);
        } elseif (stripos($name, 'backend') === 0) {
            $plugin->module = 'Backend';
            $plugin->name = substr($name, 8);
        } elseif (stripos($name, 'core') === 0) {
            $plugin->module = 'Core';
            $plugin->name = substr($name, 5);
        }

        if (empty($plugin->module)) {
            $plugin->module = 'Frontend';
            $plugin->name = $name;
        }
    }
}
