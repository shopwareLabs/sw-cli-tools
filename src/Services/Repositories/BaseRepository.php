<?php

namespace ShopwareCli\Services\Repositories;

use ShopwareCli\Services\PluginFactory;
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

    public function __construct($repository, $name, RestInterface $restService, $color=null)
    {
        $this->repository = $repository;
        $this->name = $name;
        $this->restService = $restService;
        $this->color = $color;
    }

    /**
     * @param string $sshUrl
     * @param string $httpUrl
     * @param string $name
     * @return \ShopwareCli\Struct\Plugin
     */
    public function createPlugin($sshUrl, $httpUrl, $name)
    {
        $type = array_slice(explode('\\', get_class($this)), -1);
        $type = $type[0];

        return PluginFactory::getPlugin($name, $sshUrl, $httpUrl, $this->name, $type);
    }
}
