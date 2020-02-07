<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Application;

use Composer\Autoload\ClassLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Implements a simple extension system.
 *
 * Plugins can be created in the "extensions" directory. Any folder needs to have a "Bootstrap.php" file.
 *
 * Basically you will get the DI container builder via setter injection- at this
 * point you might want to add your own dependencies to the container.
 *
 * If you implement the ConsoleAwareExtension interface, the ExtensionManager will call getConsoleCommands()
 * on your bootstrap and expect you to return a list of instantiated ConsoleCommands
 *
 * Also you can implement the interface RepositoryAwareExtension if you want to provide own repositories like
 * e.g. github
 */
class ExtensionManager
{
    /**
     * @var array
     */
    protected $extensions;

    /**
     * @var ClassLoader
     */
    private $autoLoader;

    public function __construct(ClassLoader $autoLoader)
    {
        $this->autoLoader = $autoLoader;
    }

    /**
     * Read all available plugins
     *
     * @throws \RuntimeException
     */
    public function discoverExtensions($extensionDirs): void
    {
        // iterate all plugin dirs (e.g. ~/.config/sw-cli-tools/extensions and 'Extensions' in the sw-cli-tools directory /src/
        foreach ($extensionDirs as $extensionDir) {
            /** @var \DirectoryIterator $vendorPath */
            foreach (new \DirectoryIterator($extensionDir) as $vendorPath) {
                if (!$this->isValidExtensionDir($vendorPath)) {
                    continue;
                }

                $vendorName = $vendorPath->getBasename();
                $this->registerExtensionNamespace($vendorPath->getPathname(), "{$vendorName}\\");
                $this->discoverVendorFolder($vendorPath->getPathname(), $vendorName);
            }
        }
    }

    /**
     * Instantiates a extension
     *
     * @return object
     */
    public function bootstrapExtension($className)
    {
        return new $className();
    }

    /**
     * Inject the di container into the extension
     */
    public function injectContainer(ContainerBuilder $container): void
    {
        foreach ($this->extensions as $extension) {
            if ($extension instanceof ContainerAwareExtension) {
                $extension->setContainer($container);
            }
        }
    }

    /**
     * Return all extensions
     *
     * @return object[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * Returns the plugin queried by name
     *
     * @return object
     */
    public function getExtension($name)
    {
        return $this->extensions[$name];
    }

    /**
     * Overwrite the instance of extension $name with $class
     *
     * @param string $name
     * @param object $class
     */
    public function setExtension($name, $class): void
    {
        $this->extensions[$name] = $class;
    }

    /**
     * @param string $vendorPath
     * @param string $vendorName
     *
     * @throws \RuntimeException
     */
    private function discoverVendorFolder($vendorPath, $vendorName): void
    {
        /** @var \DirectoryIterator $extensionPath */
        foreach (new \DirectoryIterator($vendorPath) as $extensionPath) {
            if (!$this->isValidExtensionDir($extensionPath) || $extensionPath->getBasename() === 'vendor') {
                continue;
            }

            if (!file_exists($extensionPath->getPathname() . '/Bootstrap.php')) {
                throw new \RuntimeException(sprintf('Could not find Bootstrap.php in %s', $extensionPath->getPathname()));
            }

            $extensionName = $extensionPath->getBasename();
            $className = "{$vendorName}\\{$extensionName}\\Bootstrap";
            $extensionInstance = $this->bootstrapExtension($className);
            $this->setExtension("{$vendorName}\\{$extensionName}", $extensionInstance);
        }
    }

    private function isValidExtensionDir(\DirectoryIterator $vendorPath): bool
    {
        return $vendorPath->isDir()
            && !$vendorPath->isDot()
            && strpos($vendorPath->getBasename(), '.') !== 0; // skip dot directories e.g. .git
    }

    /**
     * Register a namespace for given extension path
     *
     * @param string $path
     * @param string $namespace
     */
    private function registerExtensionNamespace($path, $namespace): void
    {
        $namespace = rtrim($namespace, '\\') . '\\';
        $this->autoLoader->addPsr4($namespace, $path);
    }
}
