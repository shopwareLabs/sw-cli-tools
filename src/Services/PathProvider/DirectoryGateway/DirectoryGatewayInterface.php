<?php

namespace ShopwareCli\Services\PathProvider\DirectoryGateway;

interface DirectoryGatewayInterface
{
    public function getAssetsDir();
    public function getPluginDir();
    public function getCacheDir();
    public function getConfigDir();
}
