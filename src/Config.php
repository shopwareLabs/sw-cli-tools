<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli;

use Symfony\Component\Yaml\Yaml;

/**
 * Simple config object for the config.yaml file
 */
class Config implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $configArray;

    public function __construct(ConfigFileCollector $fileCollector)
    {
        $config = [];
        foreach ($fileCollector->collectConfigFiles() as $configFile) {
            $config = \array_replace_recursive(
                $config,
                Yaml::parse(\file_get_contents($configFile), Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE)
            );
        }

        $this->configArray = $config;

        $this->validateConfig();
    }

    public function getRepositories(): array
    {
        return (array) $this->configArray['repositories'];
    }

    /**
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->configArray[$offset]);
    }

    /**
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->configArray[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->configArray[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->configArray[$offset]);
    }

    private function validateConfig(): void
    {
        if (isset($this->configArray['ShopwareInstallConfig'])) {
            throw new \RuntimeException("The config format changed, 'ShopwareInstallConfig' is not used anymore. Its former options are now distinct options 'ShopConfig', 'DatabaseConfig' and 'ShopwareInstallRepos'. Have a look at config.yaml.dist for more info.");
        }
    }
}
