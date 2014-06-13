<?php

namespace ShopwareCli\Services;

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
    /**
     * @var IoService
     */
    private $ioService;

    public function __construct(Utilities $utilities, IoService $ioService)
    {
        $this->utilities = $utilities;
        $this->ioService = $ioService;
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
            $this->ioService->write("Plugin is already installed");
            $this->utilities->changeDir($absPath);

            $this->utilities->executeCommand("git fetch origin");

            $output = $this->utilities->executeCommand("git log HEAD..origin/master --oneline");
            if (trim($output) === '') {
                $this->ioService->write("Plugin '$pluginName' ist Up to date");

                return;
            }

            $this->ioService->write("Incomming Changes:");
            $this->ioService->write($output);

            $this->utilities->executeCommand("git reset --hard HEAD");
            $this->utilities->executeCommand("git pull");
            if ($branch) {
                $this->utilities->executeCommand("git -C {$absPath} checkout {$branch}");
            }
            $this->ioService->write("Plugin '$pluginName' successfully updated.\n");

            return;
        }

        $output = $this->utilities->executeCommand("git clone $cloneUrl $absPath");
        if ($branch) {
            $this->utilities->executeCommand("git -C {$absPath} checkout {$branch}");
        }
        $branch = $branch ?: 'master';
        $this->ioService->write("Successfully checked out '$branch' for '$pluginName'\n");
    }
}
