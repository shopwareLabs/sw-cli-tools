<?php
namespace ShopwareCli;

use ShopwareCli\Services\PathProvider\PathProvider;

class ConfigFileCollector
{
    /**
     * @var PathProvider
     */
    private $pathProvider;

    /**
     * @param PathProvider $pathProvider
     */
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
        $files = array();

        // Load user file first. Its config values cannot be overwritten
        $userConfig = $this->pathProvider->getConfigPath() . '/config.yaml';
        if (file_exists($userConfig)) {
            $files[] = $userConfig;
        }

        $extensionPath = $this->pathProvider->getExtensionPath();
        $files = $this->interateVendors($extensionPath);

        // Load config.yaml.dist as latest - this way the fallback config options are defined
        $files[] = $this->pathProvider->getCliToolPath() . '/config.yaml.dist';

        return $files;
    }

    /**
     * @param string $extensionPath
     * @return string[]
     */
    private function interateVendors($extensionPath)
    {
        $files = array();

        $iter = new DirectoryFilterIterator(new \DirectoryIterator($extensionPath));
        foreach ($iter as $vendorFileInfo) {
            $files = array_merge(
                $files,
                $this->iterateExtensions($vendorFileInfo->getPathname())
            );
        }

        return $files;
    }

    /**
     * @param string $vendorPath
     * @return string[]
     */
    private function iterateExtensions($vendorPath)
    {
        $files = array();

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
