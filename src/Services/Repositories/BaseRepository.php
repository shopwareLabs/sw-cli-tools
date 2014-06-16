<?php

namespace ShopwareCli\Services\Repositories;

use ShopwareCli\Services\PluginFactory;
use ShopwareCli\Services\Rest\RestInterface;

/**
 * Base repository class providing a constructor for injection and a convenient access to the PluginFactory
 *
 * Class BaseRepository
 * @package ShopwareCli\Plugin
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var
     */
    protected $repository;

    /**
     * @var
     */
    protected $useHttp
    ;
    /**
     * @var RestInterface
     */
    protected $restService;

    /**
     * @var null
     */
    protected $color;

    /**
     * @param               $repository
     * @param               $name
     * @param RestInterface $restService
     * @param null          $color
     */
    public function __construct($repository, $name, RestInterface $restService, $color = null)
    {
        $this->repository = $repository;
        $this->name = $name;
        $this->restService = $restService;
        $this->color = $color;
    }

    /**
     * @param  string                     $sshUrl
     * @param  string                     $httpUrl
     * @param  string                     $name
     * @return \ShopwareCli\Struct\Plugin
     */
    public function createPlugin($sshUrl, $httpUrl, $name)
    {
        $type = array_slice(explode('\\', get_class($this)), -1);
        $type = $type[0];

        return PluginFactory::getPlugin($name, $sshUrl, $httpUrl, $this->name, $type);
    }

    /**
     * Very simple compare method
     *
     * @param string $searched
     * @param string $actual
     * @param  bool $exact
     * @return bool
     */
    protected function doesMatch($actual, $searched, $exact = false)
    {
        return (
            !$exact && stripos($actual, $searched) !== false
            || $exact && $searched == $actual
        );
    }
}
