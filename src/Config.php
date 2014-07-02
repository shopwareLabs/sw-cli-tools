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

    private $mainConfig;

    /**
     * @param PathProvider $pathProvider
     */
    public function __construct(PathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;

        $this->mainConfig = $this->getMainConfigFile();

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
        $files = array(
            $this->mainConfig
        );

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

    /**
     * Copy config.yaml.dist to the config directory, if the main config file does not exist, yet.
     *
     * @throws \RuntimeException
     */
    private function getMainConfigFile()
    {
        $configFile = $this->pathProvider->getConfigPath() . '/config.yaml';

        // If the main config file exists, use that
        if (file_exists($configFile)) {
            return $configFile;
        }

        // Else use config.yaml.dist
        return $this->pathProvider->getCliToolPath() . '/config.yaml.dist';
    }
}
