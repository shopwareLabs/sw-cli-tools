<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services;

use Shopware\Plugin\Struct\Plugin;

/**
 * Create a plugin struct from the passed data
 *
 * Class PluginFactory
 */
class PluginFactory
{
    /**
     * Input is a name like Backend_SwagBusinessEssentials
     *
     * @param string $name
     * @param string $sshUrl
     * @param string $httpUrl
     * @param string $repoName
     * @param $repoType
     *
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
        $plugin->isShopware6 = (bool) strpos($sshUrl, 'shopware/6/services'); // could not be position 0, so this is safe

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

        // plugin is build after new structure
        if (empty($plugin->module)) {
            $plugin->module = null;
            $plugin->name = $name;
        }
    }
}
