<?php

namespace ShopwareCli;

use ShopwareCli\Services\PathProvider\PathProvider;
use Symfony\Component\Yaml\Yaml;

/**
 * Simple config object for the config.yaml file
 *
 * Class Config
 * @package ShopwareCli
 */
class Config implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $configArray;

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

        $config = $this->getMergedConfigs($this->collectConfigFiles());
        $this->configArray = Yaml::parse($config, true);
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

    /**
     * Merge all given config.yaml files
     *
     * @param  string[] $paths
     * @return string
     */
    private function getMergedConfigs($paths)
    {
        $content = array();

        foreach ($paths as $path) {
            $content[] = file_get_contents($path);
        }

        return implode("\n", $content);
    }

    /**
     * @return mixed
     */
    public function getRepositories()
    {
        return $this->configArray['repositories'];
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->configArray[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->configArray[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->configArray[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->configArray[$offset]);
    }
}
