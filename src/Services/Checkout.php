<?php

namespace ShopwareCli\Services;

use ShopwareCli\OutputWriter\OutputWriter;
use ShopwareCli\OutputWriter\OutputWriterInterface;
use ShopwareCli\Struct\Plugin;
use ShopwareCli\Utilities;

/**
 * Checkouts a given plugin
 *
 * Class Checkout
 * @package ShopwareCli\Services
 */
class Checkout
{
    /** @var \ShopwareCli\Utilities  */
    protected $utilities;

    /** @var \ShopwareCli\OutputWriter\OutputWriterInterface  */
    protected $writer;

    public function __construct(Utilities $utilities, OutputWriterInterface $writer)
    {
        $this->utilities = $utilities;
        $this->writer = $writer;
    }

    public function checkout(Plugin $plugin, $path, $branch=null, $useHttp=false)
    {
        if ($useHttp) {
            $cloneUrl = $plugin->cloneUrlHttp;
        } else {
            $cloneUrl = $plugin->cloneUrlSsh;
        }
        $pluginName = $plugin->name;
        $destPath = $plugin->module . "/" . $plugin->name;
        $absPath = $path . '/' . $destPath;

        if (is_dir($absPath)) {
            $this->writer->write("Plugin is already installed");
            $this->utilities->changeDir($absPath);

            $this->utilities->executeCommand("git fetch origin");

            $output = $this->utilities->executeCommand("git log HEAD..origin/master --oneline");
            if (trim($output) === '') {
                $this->writer->write("Plugin '$pluginName' ist Up to date");

                return;
            }

            $this->writer->write("Incomming Changes:");
            $this->writer->write($output);

            $this->utilities->executeCommand("git reset --hard HEAD");
            $this->utilities->executeCommand("git pull");
            if ($branch) {
                $this->utilities->executeCommand("git -C {$absPath} checkout {$branch}");
            }
            $this->writer->write("Plugin '$pluginName' successfully updated.\n");

            return;
        }

        $output = $this->utilities->executeCommand("git clone $cloneUrl $absPath");
        if ($branch) {
            $this->utilities->executeCommand("git -C {$absPath} checkout {$branch}");
        }
        $branch = $branch ?: 'master';
        $this->writer->write("Successfully checked out '$branch' for '$pluginName'\n");
    }
}
