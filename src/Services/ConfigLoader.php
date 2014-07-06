<?php

namespace ShopwareCli\Services;

use ShopwareCli\Services\PathProvider\PathProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Will discover all config files and set them into the container
 *
 * Class ConfigLoader
 * @package ShopwareCli\Services
 */
class ConfigLoader
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
     * Will discover all config files and set the config options to the parameter bag
     *
     * @param $container
     */
    public function loadConfig($container)
    {
        $loader = new YamlFileLoader($container, new FileLocator());
        foreach($this->collectConfigFiles() as $file) {
            $loader->load($file);
        }
    }

    /**
     * Iterate the extension directories and return config.yaml files
     *
     * @return string[]
     */
    private function collectConfigFiles()
    {
        $files = array();

        // Load user file first. Its config values cannot be overwritten
        $userConfig = $this->pathProvider->getConfigPath() . '/config.yaml';
        if (file_exists($userConfig)) {
            $files[] = $userConfig;
        }

        // load config.yaml files from extensions
        $vendorIterator = new \DirectoryIterator($this->pathProvider->getExtensionPath());
        /** @var $vendorFileInfo \DirectoryIterator */
        foreach ($vendorIterator as $vendorFileInfo) {
            if (!$this->isValidDir($vendorFileInfo)) {
                continue;
            }
            $extensionIterator = new \DirectoryIterator($vendorFileInfo->getPathname());

            /** @var $extensionFileInfo \DirectoryIterator */
            foreach ($extensionIterator as $extensionFileInfo) {
                if (!$this->isValidDir($extensionFileInfo)) {
                    continue;
                }
                $file = $extensionFileInfo->getPathname() . '/config.yaml';

                if (file_exists($file)) {
                    $files[] = $file;
                }
            }
        }

        // Load config.yaml.dist as latest - this way the fallback config options are defined
        $files[] = $this->pathProvider->getCliToolPath() . '/config.yaml.dist';
        return $files;
    }

    /**
     * @param \DirectoryIterator $fileInfo
     *
     * @return bool
     */
    private function isValidDir(\DirectoryIterator $fileInfo)
    {
        return $fileInfo->isDir() && !$fileInfo->isDot() && stripos(
            $fileInfo->getBasename(),
            '.'
        ) !== 0; // skip dot directories e.g. .git
    }
}