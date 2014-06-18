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

        $this->enforceMainConfigFile();

        $config = $this->getMergedConfigs($this->collectConfigFiles());
        $this->configArray = Yaml::parse($config, true);
    }

    /**
     * Iterate the plugin directories and return config.yaml files
     *
     * @return string[]
     */
    private function collectConfigFiles()
    {
        $files = array(
            $this->pathProvider->getConfigPath() . '/config.yaml'
        );

        $iterator = new \DirectoryIterator($this->pathProvider->getExtensionPath());

        /** @var $fileInfo \DirectoryIterator */
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile() || $fileInfo->isDot()) {
                continue;
            }

            $file = $fileInfo->getPathName() . '/config.yaml';

            if (file_exists($file)) {
                $files[] = $file;
            }
        }

        return $files;
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
    private function enforceMainConfigFile()
    {
        $configFile = $this->pathProvider->getConfigPath() . '/config.yaml';
        if (!file_exists($configFile)) {
            copy($this->pathProvider->getCliToolPath() . '/config.yaml.dist', $configFile);
            if (!file_exists($configFile)) {
                throw new \RuntimeException("Could not find '{$configFile}'");
            }
        }
    }
}
