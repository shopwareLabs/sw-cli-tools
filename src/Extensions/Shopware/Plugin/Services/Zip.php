<?php

namespace Shopware\Plugin\Services;

use ShopwareCli\Struct\Plugin;
use ShopwareCli\Utilities;

/**
 * Checks out a given plugin and creates a zip file from it
 *
 * Class Zip
 * @package ShopwareCli\Services
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
     * @param Checkout  $checkout
     * @param Utilities $utilities
     */
    public function __construct(Checkout $checkout, Utilities $utilities)
    {
        $this->checkout = $checkout;
        $this->utilities = $utilities;
    }

    /**
     * @param Plugin $plugin
     * @param        $path
     * @param        $zipTo
     * @param        $branch
     * @param bool   $useHttp
     *
     * @return string
     */
    public function zip(Plugin $plugin, $path, $zipTo, $branch, $useHttp = false)
    {
        $this->checkout->checkout($plugin, $path, $branch, $useHttp);

        $outputFile = "{$zipTo}/{$plugin->name}.zip";
        $this->utilities->executeCommand("zip -r $outputFile {$plugin->module} -x *.git*");

        return $outputFile;
    }
}
