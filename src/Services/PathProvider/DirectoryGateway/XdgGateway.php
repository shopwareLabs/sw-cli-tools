<?php

namespace ShopwareCli\Services\PathProvider\DirectoryGateway;

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

        $this->checkDirectories();
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
    public function getPluginDir()
    {
        return $this->xdg->getHomeConfigDir() . '/sw-cli-tools/plugins';
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
     * Make sure, that the required directories do actually exist
     *
     * @throws \RuntimeException
     */
    protected function checkDirectories()
    {
        foreach (array(
                     $this->getAssetsDir(),
                     $this->getCacheDir(),
                     $this->getPluginDir(),
                     $this->getConfigDir()
                 ) as $dir) {
            if (!is_dir($dir)) {
                $success = @mkdir($dir, 0777, true);
                if (!$success) {
                    throw new \RuntimeException("Could not find / create $dir");
                }
            }
        }
    }
}
