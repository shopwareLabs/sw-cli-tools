<?php

namespace ShopwareCli\Plugin;

use ShopwareCli\Services\Rest\RestInterface;

/**
 * Base repository class
 *
 * Class BaseRepository
 * @package ShopwareCli\Plugin
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected $name;
    protected $repository;
    protected $useHttp;
    protected $restService;
    protected $color;

    public function __construct($repository, $useHttp, $name, RestInterface $restService, $color=null)
    {
        $this->repository = $repository;
        $this->useHttp = $useHttp;
        $this->name = $name;
        $this->restService = $restService;
        $this->color = $color;
    }

    public function createPlugin($url, $name)
    {
        $type = array_slice(explode('\\', get_class($this)), -1);
        $type = $type[0];

        return PluginFactory::getPlugin($name, $url, $this->name, $type);
    }

    abstract public function getPluginByName($name);

    abstract public function getPlugins();

}
