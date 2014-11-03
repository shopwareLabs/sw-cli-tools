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
     * @param ConfigFileCollector $fileCollector
     */
    public function __construct(ConfigFileCollector $fileCollector)
    {
        $config = $this->getMergedConfigs($fileCollector->collectConfigFiles());
        $this->configArray = Yaml::parse($config, true);

        $this->validateConfig();
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

    private function validateConfig()
    {
        if (isset($this->configArray['ShopwareInstallConfig'])) {
            throw new \RuntimeException("The config format changed, 'ShopwareInstallConfig' is not used anymore. Its former options are now distinct options 'ShopConfig', 'DatabaseConfig' and 'ShopwareInstallRepos'. Have a look at config.yaml.dist for more info.");
        }
    }

}
