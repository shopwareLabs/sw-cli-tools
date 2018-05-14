<?php

namespace ShopwareCli;

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
        $config = [];
        foreach ($fileCollector->collectConfigFiles() as $configFile) {
            $config = array_replace_recursive($config, Yaml::parse(file_get_contents($configFile), true));
        }

        $this->configArray = $config;

        $this->validateConfig();
    }

    /**
     * @return mixed
     */
    public function getRepositories()
    {
        return (array) $this->configArray['repositories'];
    }

    /**
     * @param $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->configArray[$offset]);
    }

    /**
     * @param $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (array_key_exists($offset, $this->configArray)) {
            return $this->configArray[$offset];
        }

        return null;
    }

    /**
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value)
    {
        $this->configArray[$offset] = $value;
    }

    /**
     * @param $offset
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
