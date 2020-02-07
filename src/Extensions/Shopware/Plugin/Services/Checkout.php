<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services;

use Shopware\Plugin\Struct\Plugin;
use ShopwareCli\Services\GitUtil;
use ShopwareCli\Services\IoService;
use ShopwareCli\Utilities;

/**
 * Checkouts a given plugin
 */
class Checkout
{
    /**
     * @var Utilities
     */
    protected $utilities;

    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @var GitUtil
     */
    private $gitUtil;

    public function __construct(Utilities $utilities, GitUtil $gitUtil, IoService $ioService)
    {
        $this->utilities = $utilities;
        $this->ioService = $ioService;
        $this->gitUtil = $gitUtil;
    }

    /**
     * @param string $path
     * @param string $branch
     * @param bool   $useHttp
     */
    public function checkout(Plugin $plugin, $path, $branch = null, $useHttp = false): void
    {
        if ($useHttp) {
            $cloneUrl = $plugin->cloneUrlHttp;
        } else {
            $cloneUrl = $plugin->cloneUrlSsh;
        }
        $pluginName = $plugin->name;

        if ($plugin->module) {
            $destPath = $plugin->module . '/' . $pluginName;
        } else {
            $destPath = $pluginName;
        }

        $absPath = $path . '/' . $destPath;

        if (is_dir($absPath)) {
            $this->updatePlugin($branch, $absPath, $pluginName);

            return;
        }

        $this->installPlugin($branch, $cloneUrl, $absPath, $pluginName);
    }

    /**
     * @param string $branch
     * @param string $absPath
     * @param string $pluginName
     */
    private function updatePlugin($branch, $absPath, $pluginName): void
    {
        $this->ioService->writeln('Plugin is already installed');
        $this->utilities->changeDir($absPath);

        $this->gitUtil->run('fetch --progress origin');

        $output = $this->gitUtil->run('log HEAD..origin/master --oneline');
        if (trim($output) === '') {
            $this->ioService->writeln("Plugin '$pluginName' is up to date");
            if ($branch) {
                $this->gitUtil->run("checkout {$branch}");
            }

            return;
        }

        $this->ioService->writeln('Incoming changes:');
        $this->ioService->writeln($output);

        $this->gitUtil->run('reset --hard HEAD');
        $this->gitUtil->run('pull');
        if ($branch) {
            // the CWD change is a fix for older versions of GIT which do not support the -C flag
            $cwd = getcwd();
            $this->utilities->changeDir($absPath);
            $this->gitUtil->run("checkout {$branch}");
            $this->utilities->changeDir($cwd);
        }
        $this->ioService->writeln("Plugin '$pluginName' successfully updated.\n");
    }

    /**
     * @param string $branch
     * @param string $cloneUrl
     * @param string $absPath
     * @param string $pluginName
     */
    private function installPlugin($branch, $cloneUrl, $absPath, $pluginName): void
    {
        $this->gitUtil->run("clone  --progress $cloneUrl $absPath");
        if ($branch) {
            // the CWD change is a fix for older versions of GIT which do not support the -C flag
            $cwd = getcwd();
            $this->utilities->changeDir($absPath);
            $this->gitUtil->run("checkout {$branch}");
            $this->utilities->changeDir($cwd);
        }
        $branch = $branch ?: 'master';
        $this->ioService->writeln("Successfully checked out '$branch' for '$pluginName'\n");
    }
}
