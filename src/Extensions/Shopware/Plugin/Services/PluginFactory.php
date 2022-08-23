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
 */
class PluginFactory
{
    public const SW6_PLUGIN_PATHS = [
        'shopware/6/services',
        'shopware/6/enterprise',
    ];

    /**
     * Input is a name like Backend_SwagBusinessEssentials
     *
     * @param string $name
     * @param string $sshUrl
     * @param string $httpUrl
     * @param string $repoName
     */
    public static function getPlugin($name, $sshUrl, $httpUrl, $repoName, $repoType = null): Plugin
    {
        $plugin = new Plugin();

        self::setPluginModuleFromName($name, $plugin);

        $plugin->cloneUrlSsh = $sshUrl;
        $plugin->cloneUrlHttp = $httpUrl;
        $plugin->repository = $repoName;
        $plugin->repoType = $repoType;

        $preg = \implode('|', self::SW6_PLUGIN_PATHS);
        $preg = '/' . \str_replace('/', '\/', $preg) . '/';
        $plugin->isShopware6 = (bool) \preg_match($preg, $sshUrl);

        return $plugin;
    }

    /**
     * @param string $name
     */
    private static function setPluginModuleFromName($name, Plugin $plugin): void
    {
        if (\stripos($name, 'frontend') === 0) {
            $plugin->module = 'Frontend';
            $plugin->name = \substr($name, 9);
        } elseif (\stripos($name, 'backend') === 0) {
            $plugin->module = 'Backend';
            $plugin->name = \substr($name, 8);
        } elseif (\stripos($name, 'core') === 0) {
            $plugin->module = 'Core';
            $plugin->name = \substr($name, 5);
        }

        // plugin is build after new structure
        if (empty($plugin->module)) {
            $plugin->module = null;
            $plugin->name = $name;
        }
    }
}
