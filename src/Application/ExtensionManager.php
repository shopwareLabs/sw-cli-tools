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
     * @throws \RuntimeException
     */
    public function discoverExtensions($extensionDirs)
    {
        // iterate all plugin dirs (e.g. ~/.config/sw-cli-tools/extensions and 'Extensions' in the sw-cli-tools directory /src/
        foreach ($extensionDirs as $extensionDir) {
            // iterate all vendor folders
            foreach (scandir($extensionDir) as $vendorFolder) {
                $vendorPath = $extensionDir . '/' . $vendorFolder;

                // skip files and dot-folders
                if (!is_dir($vendorPath) ||strpos($vendorFolder, '.') === 0) {
                    continue;
                }

                $this->registerExtensionNamespace($vendorPath, "{$vendorFolder}\\");

                // iterate all extension folders
                foreach (scandir($vendorPath) as $extensionFolder) {
                    $extensionPath = $vendorPath . '/' . $extensionFolder;

                    // skip files and dot-folders
                    if (!is_dir($extensionPath) ||strpos($extensionFolder, '.') === 0) {
                        continue;
                    }

                    if (!file_exists("{$extensionPath}/Bootstrap.php")) {
                        throw new \RuntimeException("Could not find Bootstrap.php in {$extensionPath}");
                    }

                    $className = "{$vendorFolder}\\{$extensionFolder}\\Bootstrap";
                    $extensionInstance = $this->bootstrapExtension($className);
                    $this->setExtension("{$vendorFolder}\\{$extensionFolder}", $extensionInstance);
                }
            }
        }
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
