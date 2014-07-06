<?php

namespace ShopwareCli;

use ShopwareCli\Services\PathProvider\PathProvider;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Yaml\Yaml;

/**
 * Simple config object for the config.yaml file
 *
 * Class Config
 * @package ShopwareCli
 * @deprecated Use the container for config handling instead
 */
class Config implements \ArrayAccess
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @param PathProvider $pathProvider
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return mixed
     */
    public function getRepositories()
    {
        return $this->container->getParameter('repositories');
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        $var = $this->container->getParameter($offset);
        return isset($var);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->container->getParameter($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->container->setParameter($offset, $value);
    }

    /**
     * @param mixed $offset
     * @throws \RuntimeException
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException("Not implemented");
    }
}
