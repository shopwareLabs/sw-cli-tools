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

    public function __construct(PathProvider $pathProvider)
    {
        $paths = $this->collectConfigFiles($pathProvider);
        $config = $this->getMergedConfigs($paths);

        $this->configArray = Yaml::parse($config, true);
    }

    private function collectConfigFiles(PathProvider $pathProvider)
    {
        $configPath = $pathProvider->getConfigPath();

        $configFile = $configPath . '/config.yaml';

        if (!file_exists($configFile)) {
            copy($pathProvider->getCliToolPath() . '/config.yaml.dist', $configFile);
            if (!file_exists($configFile)) {
                throw new \RuntimeException("Could not find '{$configFile}'");
            }
        }

        $files = array(
            $configPath
        );

        $iterator = new \DirectoryIterator($pathProvider->getPluginPath());
        foreach ($iterator as $fileInfo) {
            $file = $fileInfo->getPathName() . '/config.yaml';

            if (file_exists($file)) {
                $files[] = $file;
            }
        }

        return $files;
    }

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


}