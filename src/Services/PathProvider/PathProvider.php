<?php

namespace ShopwareCli\Services\PathProvider;

use ShopwareCli\Services\PathProvider\DirectoryGateway\DirectoryGatewayInterface;

class PathProvider
{
    /**
     * @var DirectoryGateway\DirectoryGatewayInterface
     */
    private $directoryGateway;

    public function __construct(DirectoryGatewayInterface $directoryGateway)
    {
        $this->directoryGateway = $directoryGateway;
    }

    /**
     * @return mixed
     */
    public function getCliToolPath()
    {
        return dirname(dirname(dirname(__DIR__)));
    }

    public function getCachePath()
    {
        return $this->directoryGateway->getCacheDir();
    }

    public function getAssetsPath()
    {
        return $this->directoryGateway->getAssetsDir();
    }

    public function getExtensionPath()
    {
        return $this->directoryGateway->getExtensionDir();
    }

    public function getConfigPath()
    {
        return $this->directoryGateway->getConfigDir();
    }
}
