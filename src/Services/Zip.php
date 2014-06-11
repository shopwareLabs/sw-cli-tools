<?php

namespace ShopwareCli\Services;

use ShopwareCli\OutputWriter\OutputWriterInterface;
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
    /** @var \ShopwareCli\Services\Checkout  */
    protected $checkout;
    /** @var \ShopwareCli\OutputWriter\OutputWriterInterface  */
    protected $writer;
    /** @var \ShopwareCli\Utilities  */
    protected $utilities;

    public function __construct(Checkout $checkout, Utilities $utilities, OutputWriterInterface $writer)
    {
        $this->checkout = $checkout;
        $this->writer = $writer;
        $this->utilities = $utilities;
    }

    function zip(Plugin $plugin, $path, $zipTo, $branch) {
        $this->checkout->checkout($plugin, $path, $branch);

        $outputFile = "{$zipTo}/{$plugin->name}.zip";
        $this->utilities->executeCommand("zip -r $outputFile {$plugin->module} -x *.git*");
        return $outputFile;
    }
}