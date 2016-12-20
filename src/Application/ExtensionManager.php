<?php

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
 *
 * Class ExtensionManager
 * @package ShopwareCli\Application
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

    /**
     * @param ClassLoader $autoLoader
     */
    public function __construct(ClassLoader $autoLoader)
    {
        $this->autoLoader = $autoLoader;
    }

    /**
     * Read all available plugins
     *
     * @param $extensionDirs
     * @throws \RuntimeException
     */
    public function discoverExtensions($extensionDirs)
    {
        // iterate all plugin dirs (e.g. ~/.config/sw-cli-tools/extensions and 'Extensions' in the sw-cli-tools directory /src/
        foreach ($extensionDirs as $extensionDir) {
            /** @var $vendorPath \DirectoryIterator */
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
     * @param string $vendorPath
     * @param string $vendorName
     *
     * @throws \RuntimeException
     */
    private function discoverVendorFolder($vendorPath, $vendorName)
    {
        /** @var $extensionPath \DirectoryIterator */
        foreach (new \DirectoryIterator($vendorPath) as $extensionPath) {
            if (!$this->isValidExtensionDir($extensionPath)) {
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

    /**
     * @param \DirectoryIterator $vendorPath
     *
     * @return bool
     */
    private function isValidExtensionDir(\DirectoryIterator $vendorPath)
    {
        return $vendorPath->isDir()
            && !$vendorPath->isDot()
            && stripos($vendorPath->getBasename(), '.') !== 0; // skip dot directories e.g. .git
    }

    /**
     * Register a namespace for given extension path
     *
     * @param string $path
     * @param string $namespace
     */
    private function registerExtensionNamespace($path, $namespace)
    {
        $namespace = rtrim($namespace, '\\') . '\\';
        $this->autoLoader->addPsr4($namespace, $path);
    }

    /**
     * Instantiates a extension
     *
     * @param $className
     * @return object
     */
    public function bootstrapExtension($className)
    {
        $extension = new $className();

        return $extension;
    }

    /**
     * Inject the di container into the extension
     *
     * @param ContainerBuilder $container
     */
    public function injectContainer(ContainerBuilder $container)
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
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Returns the plugin queried by name
     *
     * @param $name
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
    public function setExtension($name, $class)
    {
        $this->extensions[$name] = $class;
    }
}
