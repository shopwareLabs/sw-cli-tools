<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli;

use ShopwareCli\Services\PathProvider\PathProvider;

class ConfigFileCollector
{
    /**
     * @var PathProvider
     */
    private $pathProvider;

    public function __construct(PathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }

    /**
     * Iterate the extension directories and return config.yaml files
     *
     * @return string[]
     */
    public function collectConfigFiles()
    {
        $files = [];

        // Load config.yaml.dist as latest - this way the fallback config options are defined
        $files[] = $this->pathProvider->getCliToolPath() . '/config.yaml.dist';

        $extensionPath = $this->pathProvider->getExtensionPath();
        $files = array_merge($files, $this->iterateVendors($extensionPath));
        $files = array_merge($files, $this->iterateVendors(__DIR__ . '/Extensions'));

        // Load user file first. Its config values cannot be overwritten
        $userConfig = $this->pathProvider->getConfigPath() . '/config.yaml';
        if (file_exists($userConfig)) {
            $files[] = $userConfig;
        }

        return $files;
    }

    /**
     * @param string $extensionPath
     *
     * @return string[]
     */
    private function iterateVendors($extensionPath): array
    {
        $files = [];

        if (!is_dir($extensionPath)) {
            return [];
        }

        $iter = new DirectoryFilterIterator(new \DirectoryIterator($extensionPath));
        foreach ($iter as $vendorFileInfo) {
            $file = $vendorFileInfo->getPathname() . '/config.yaml';
            if (file_exists($file)) {
                $files[] = $file;
            }

            $files = array_merge(
                $files,
                $this->iterateExtensions($vendorFileInfo->getPathname())
            );
        }

        return $files;
    }

    /**
     * @param string $vendorPath
     *
     * @return string[]
     */
    private function iterateExtensions($vendorPath): array
    {
        $files = [];

        $iter = new DirectoryFilterIterator(new \DirectoryIterator($vendorPath));
        foreach ($iter as $extensionFileInfo) {
            $file = $extensionFileInfo->getPathname() . '/config.yaml';
            if (file_exists($file)) {
                $files[] = $file;
            }
        }

        return $files;
    }
}
