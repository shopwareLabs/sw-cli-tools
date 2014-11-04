<?php

namespace ShopwareCli\Services\PathProvider\DirectoryGateway;

use XdgBaseDir\Xdg;

class XdgGateway implements DirectoryGatewayInterface
{
    /**
     * @var Xdg
     */
    private $xdg;

    /**
     * @param Xdg $xdg
     */
    public function __construct(Xdg $xdg)
    {
        $this->xdg = $xdg;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetsDir()
    {
        return $this->xdg->getHomeDataDir() . '/sw-cli-tools/assets';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionDir()
    {
        return $this->xdg->getHomeConfigDir() . '/sw-cli-tools/extensions';
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->xdg->getHomeCacheDir() . '/sw-cli-tools';
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigDir()
    {
        return $this->xdg->getHomeConfigDir() . '/sw-cli-tools';
    }

    /**
     * @return string
     */
    public function getRuntimeDir()
    {
        return $this->xdg->getRuntimeDir(false);
    }
}
