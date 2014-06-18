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
        return is_readable($path . '/shopware.php');
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
            "Path to your Shopware installation: ",
            array($this, 'validateShopwarePath')
        );
    }

    /**
     * Shopware path validator - can be used in askAndValidate methods
     *
     * @param  string            $shopwarePath
     * @return string
     * @throws \RuntimeException
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
     * This could / should be switched do symfony's process component
     * Currently it seems to have issues with realtime output,
     * so keeping "exec" for the time being
     *
     * @param  string            $cmd
     * @param  bool              $mayFail
     * @return string
     * @throws \RuntimeException
     *
     * @deprecated Use the new ProcessExecutor instead
     */
    public function executeCommand($cmd, $mayFail = false)
    {
        $output = array();
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        if (!$mayFail && $returnCode !== 0) {
            throw new \RuntimeException(
                sprintf("An exception occurred: %s", implode("\n", $output))
            );
        }

        return implode("\n", $output) . "\n";
    }

    /**
     * Clears the screen in the terminal
     */
    public function cls()
    {
        system('clear');
    }

    /**
     * Changes a directory
     *
     * @param $path
     * @throws \RuntimeException
     */
    public function changeDir($path)
    {
        if (!chdir($path)) {
            throw new \RuntimeException("Could not cd into '$path''");
        }
    }
}
