<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    public function getCliToolPath(): string
    {
        return \dirname(__DIR__, 3);
    }

    public function getCachePath(): string
    {
        return $this->directoryGateway->getCacheDir();
    }

    public function getAssetsPath(): string
    {
        return $this->directoryGateway->getAssetsDir();
    }

    public function getExtensionPath(): string
    {
        return $this->directoryGateway->getExtensionDir();
    }

    public function getConfigPath(): string
    {
        return $this->directoryGateway->getConfigDir();
    }

    public function getRuntimeDir(): string
    {
        // temporary fix: selinux seems to interfere with executables in /run/user on some systems
        return '/tmp';
        // return $this->directoryGateway->getRuntimeDir();
    }
}
