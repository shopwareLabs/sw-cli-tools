<?php

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
     * Checks if a given path is a shopware installation
     * (by checking for shopware.php)
     *
     * @param  string $path
     * @return bool
     */
    public function isShopwareInstallation($path)
    {
        return is_readable($path.'/shopware.php');
    }

    /**
     * Ask for a valid shopware path until the user enters it
     *
     * @param  string $shopwarePath
     * @return string
     */
    public function getValidShopwarePath($shopwarePath = null)
    {
        if (!$shopwarePath) {
            $shopwarePath = realpath(getcwd());
        }

        if ($this->isShopwareInstallation($shopwarePath)) {
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
     * @param  string $shopwarePath
     * @throws \RuntimeException
     * @return string
     */
    public function validateShopwarePath($shopwarePath)
    {
        $shopwarePathReal = realpath($shopwarePath);

        if (!$this->isShopwareInstallation($shopwarePathReal)) {
            throw new \RuntimeException(
                "{$shopwarePathReal} is not a valid shopware path"
            );
        }

        return $shopwarePathReal;
    }

    /**
     * Changes a directory
     *
     * @param  string $path
     * @throws \RuntimeException
     */
    public function changeDir($path)
    {
        if (!chdir($path)) {
            throw new \RuntimeException("Could not cd into '$path''");
        }
    }
}
