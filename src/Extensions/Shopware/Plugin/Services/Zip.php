<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services;

use Shopware\Plugin\Struct\Plugin;
use ShopwareCli\Extensions\Shopware\Plugin\Services\DependencyManager;
use ShopwareCli\Services\ProcessExecutor;
use ShopwareCli\Utilities;

/**
 * Checks out a given plugin and creates a zip file from it
 */
class Zip
{
    /**
     * @var Checkout
     */
    protected $checkout;

    /**
     * @var Utilities
     */
    protected $utilities;

    /**
     * @var ProcessExecutor
     */
    private $processExecutor;

    /**
     * @var DependencyManager
     */
    private $dependencyManager;

    public function __construct(
        Checkout $checkout,
        Utilities $utilities,
        ProcessExecutor $processExecutor,
        DependencyManager $dependencyManager
    ) {
        $this->checkout = $checkout;
        $this->utilities = $utilities;
        $this->processExecutor = $processExecutor;
        $this->dependencyManager = $dependencyManager;
    }

    /**
     * @param string $path
     * @param string $zipTo
     * @param string $branch
     * @param bool   $useHttp
     */
    public function zip(Plugin $plugin, $path, $zipTo, $branch, $useHttp = false): string
    {
        $this->checkout->checkout($plugin, $path, $branch, $useHttp);

        if ($plugin->module) {
            $pluginPath = $path . '/' . $plugin->module . '/' . $plugin->name;
        } else {
            $pluginPath = $path . '/' . $plugin->name;
        }

        $blackListPath = $pluginPath . '/.sw-zip-blacklist';

        if (\file_exists($blackListPath)) {
            $blackList = \file_get_contents($blackListPath);
            $blackList = \array_filter(\explode("\n", $blackList));

            foreach ($blackList as $item) {
                $this->processExecutor->execute('rm -rf ' . $pluginPath . '/' . $item);
            }
            $this->processExecutor->execute('rm -rf ' . $blackListPath);
        }

        $this->dependencyManager->manageDependencies($plugin, $pluginPath);

        $outputFile = "{$zipTo}/{$plugin->name}.zip";

        if ($plugin->module) {
            $this->zipDir($plugin->module, $outputFile);
        } else {
            $this->zipDir($plugin->name, $outputFile);
        }

        return $outputFile;
    }

    /**
     * @param string $directory
     */
    public function zipDir($directory, $outputFile): void
    {
        $this->processExecutor->execute("zip -r $outputFile $directory -x *.git*");
    }
}
