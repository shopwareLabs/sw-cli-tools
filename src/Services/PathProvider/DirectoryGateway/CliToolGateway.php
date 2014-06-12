<?php

namespace ShopwareCli\Services\PathProvider\DirectoryGateway;

class CliToolGateway implements DirectoryGatewayInterface
{

    /**
     * @return mixed
     */
    public function getBasePath()
    {
        return dirname(dirname(dirname(dirname(__DIR__))));
    }

    public function getCacheDir()
    {
        return $this->getBasePath() . '/cache';
    }

    public function getAssetsDir()
    {
        return $this->getBasePath() . '/assets';
    }

    public function getPluginDir()
    {
        return $this->getBasePath() . '/plugins';
    }

    public function getConfigDir()
    {
        return $this->getBasePath();
    }

}
