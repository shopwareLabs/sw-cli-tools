<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services;

use Shopware\Plugin\Struct\PluginBootstrap;

/**
 * Extract info from a shopware plugin bootstrap
 *
 * Class BootstrapInfo
 */
class BootstrapInfo
{
    /**
     * Analyze a bootstrap file and return a PluginBootstrap struct
     *
     * @param $bootstrapFile
     *
     * @return PluginBootstrap
     */
    public function analyze($bootstrapFile)
    {
        $content = file_get_contents($bootstrapFile);

        list($namespace, $name) = $this->analyzeClass($content);

        $info = new PluginBootstrap();
        $info->module = $namespace;
        $info->name = $name;

        return $info;
    }

    /**
     * Return name and namespace from a plugin by regex-ing the class name
     *
     * @param string $content
     *
     * @throws \RuntimeException
     *
     * @return string[]
     */
    private function analyzeClass($content)
    {
        $pattern = '#.*Shopware_Plugins_(?P<namespace>[a-zA-Z0-9]+)_(?P<name>[a-zA-Z0-9]+)_Bootstrap.*#';
        $matches = [];
        preg_match($pattern, $content, $matches);
        if (empty($matches)) {
            throw new \RuntimeException('Could not analyze bootstrap');
        }

        return [$matches['namespace'], $matches['name']];
    }
}
