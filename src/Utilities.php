<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli;

use ShopwareCli\Services\IoService;

class Utilities
{
    /**
     * @var Services\IoService
     */
    private $ioService;

    public function __construct(IoService $ioService)
    {
        $this->ioService = $ioService;
    }

    /**
     * Checks if a given path is a Shopware 5 installation
     *
     * @param string $path
     */
    public function isShopware5Installation($path): bool
    {
        return is_readable($path . '/shopware.php');
    }

    /**
     * Checks if a given path is a Shopware 6 installation
     *
     * @param string $path
     */
    public function isShopware6Installation($path): bool
    {
        return is_dir($path . '/vendor/shopware/platform') || is_dir($path . '/vendor/shopware/core');
    }

    /**
     * Ask for a valid Shopware path until the user enters it
     *
     * @param string|null $shopwarePath
     */
    public function getValidShopwarePath($shopwarePath = null): string
    {
        if ($shopwarePath === null) {
            $shopwarePath = realpath(getcwd());
        }

        if ($this->isShopware5Installation($shopwarePath)) {
            return $shopwarePath;
        }

        if ($this->isShopware6Installation($shopwarePath)) {
            return $shopwarePath;
        }

        return $this->ioService->askAndValidate(
            'Path to your Shopware installation: ',
            [$this, 'validateShopwarePath']
        );
    }

    /**
     * Shopware path validator - can be used in askAndValidate methods
     *
     * @param string $shopwarePath
     *
     * @throws \RuntimeException
     */
    public function validateShopwarePath($shopwarePath): string
    {
        $shopwarePathReal = realpath($shopwarePath);

        if ($this->isShopware5Installation($shopwarePathReal)) {
            return $shopwarePathReal;
        }

        if ($this->isShopware6Installation($shopwarePathReal)) {
            return $shopwarePathReal;
        }

        throw new \RuntimeException(
            "{$shopwarePathReal} is not a valid Shopware path"
        );
    }

    /**
     * Changes a directory
     *
     * @param string $path
     *
     * @throws \RuntimeException
     */
    public function changeDir($path): void
    {
        if (!chdir($path)) {
            throw new \RuntimeException("Could not cd into '$path''");
        }
    }
}
