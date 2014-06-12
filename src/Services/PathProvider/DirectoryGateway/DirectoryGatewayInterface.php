<?php

namespace ShopwareCli\Services\PathProvider\DirectoryGateway;

interface DirectoryGatewayInterface
{
    /**
     * @return string
     */
    public function getAssetsDir();

    /**
     * @return string
     */
    public function getPluginDir();

    /**
     * @return string
     */
    public function getCacheDir();

    /**
     * @return string
     */
    public function getConfigDir();
}
