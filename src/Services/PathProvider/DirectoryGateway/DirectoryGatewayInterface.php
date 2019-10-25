<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function getExtensionDir();

    /**
     * @return string
     */
    public function getCacheDir();

    /**
     * @return string
     */
    public function getConfigDir();

    /**
     * @return string
     */
    public function getRuntimeDir();
}
