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
    protected $configArray;
    /**
     * @var Services\PathProvider\PathProvider
     */
    private $pathProvider;

    public function __construct(PathProvider $pathProvider)
    {
        $this->enforceMainConfigFile();

        $config = $this->getMergedConfigs($this->collectConfigFiles());

        $this->configArray = Yaml::parse($config, true);
        $this->pathProvider = $pathProvider;
    }

    /**
     * Iterate the plugin directories and return config.yaml files
     *
     * @return array
     */
    private function collectConfigFiles()
    {
        $files = array(
            $this->pathProvider->getConfigPath()
        );

        $iterator = new \DirectoryIterator($this->pathProvider->getPluginPath());
        foreach ($iterator as $fileInfo) {
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
     * @param $paths
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

    public function getRepositories()
    {
        return $this->configArray['repositories'];
    }

    public function offsetExists($offset)
    {
        return isset($this->configArray[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->configArray[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->configArray[$offset] = $value;
    }

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
