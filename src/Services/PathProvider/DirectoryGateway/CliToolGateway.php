<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\PathProvider\DirectoryGateway;

class CliToolGateway implements DirectoryGatewayInterface
{
    /**
     * @var string
     */
    private $basePath;

    /**
     * @param string $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = \rtrim($basePath, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->getBasePath() . '/cache';
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetsDir()
    {
        return $this->getBasePath() . '/assets';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionDir()
    {
        return $this->getBasePath() . '/extensions';
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigDir()
    {
        return $this->getBasePath();
    }

    public function getRuntimeDir()
    {
        throw new \RuntimeException('not implemented');
    }
}
