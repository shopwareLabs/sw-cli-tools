<?php

namespace Shopware\Plugin\Services;

use Shopware\Plugin\Struct\Plugin;
use ShopwareCli\Services\ProcessExecutor;
use ShopwareCli\Utilities;

/**
 * Checks out a given plugin and creates a zip file from it
 *
 * Class Zip
 * @package Shopware\Plugin\Services
 */
class Zip
{
    /**
     * @var Checkout
     */
    protected $checkout;

    /**
     * @var \ShopwareCli\Utilities
     */
    protected $utilities;

    /**
     * @var ProcessExecutor
     */
    private $processExecutor;

    /**
     * @param Checkout $checkout
     * @param Utilities $utilities
     * @param ProcessExecutor $processExecutor
     */
    public function __construct(Checkout $checkout, Utilities $utilities, ProcessExecutor $processExecutor)
    {
        $this->checkout = $checkout;
        $this->utilities = $utilities;
        $this->processExecutor = $processExecutor;
    }

    /**
     * @param Plugin $plugin
     * @param        $path
     * @param        $zipTo
     * @param        $branch
     * @param bool $useHttp
     *
     * @return string
     */
    public function zip(Plugin $plugin, $path, $zipTo, $branch, $useHttp = false)
    {
        $this->checkout->checkout($plugin, $path, $branch, $useHttp);

        $outputFile = "{$zipTo}/{$plugin->name}.zip";
        $this->zipDir($plugin->module, $outputFile);

        return $outputFile;
    }

    /**
     * @param string $directory
     * @param mixed $outputFile
     */
    public function zipDir($directory, $outputFile)
    {
        $this->processExecutor->execute("zip -r $outputFile $directory -x *.git*");
    }
}
