<?php

namespace ShopwareCli\Application;
use Composer\Autoload\ClassLoader;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Implements a simple plugin system.
 *
 * Plugins can be created in the "plugins" directory. Any folder needs to have a "Bootstrap.php" file.
 *
 * Basically you will get the DI container builder via setter injection- at this
 * point you might want to add your own dependencies to the container.
 *
 * If you implement the ConsoleAwarePlugin interface, the PluginManager will call getConsoleCommands()
 * on your bootstrap and expect you to return a list of instantiated ConsoleCommands
 *
 * Also you can implement the interface RepositoryAwarePlugin if you want to provide own repositories like
 * e.g. github
 *
 * Class PluginManager
 * @package ShopwareCli\Application
 */
class PluginManager
{
    protected $plugins;

    /**
     * @var \Composer\Autoload\ClassLoader
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
    public function discoverPlugins($pluginDirs)
    {
        // iterate all plugin dirs (e.g. ~/.config/sw-cli-tools/plugins and 'plugins' in the sw-cli-tools main directory / phar
        foreach ($pluginDirs as $pluginDir) {
            // iterate all vendor folders
            foreach (scandir($pluginDir) as $vendorFolder) {
                $vendorPath = $pluginDir . '/' . $vendorFolder;

                // skip files and dot-folders
                if (!is_dir($vendorPath) ||strpos($vendorFolder, '.') === 0) {
                    continue;
                }

                $this->registerPluginNamespace($vendorPath, "{$vendorFolder}\\");

                // iterate all plugin folders
                foreach (scandir($vendorPath) as $pluginFolder) {
                    $pluginPath = $vendorPath . '/' . $pluginFolder;

                    // skip files and dot-folders
                    if (!is_dir($pluginPath) ||strpos($pluginFolder, '.') === 0) {
                        continue;
                    }

                    if (!file_exists("{$pluginPath}/Bootstrap.php")) {
                        throw new \RuntimeException("Could not find Bootstrap.php in {$pluginPath}");
                    }

                    $className = "{$vendorFolder}\\{$pluginFolder}\\Bootstrap";
                    $pluginInstance = $this->bootstrapPlugin($className);
                    $this->setPlugin("{$vendorFolder}\\{$pluginFolder}", $pluginInstance);
                }
            }
        }
    }

    /**
     * Register a namespace for given plugin path
     *
     * @param $path
     * @param $namespace
     */
    private function registerPluginNamespace($path, $namespace)
    {
        $namespace = rtrim($namespace, '\\') . '\\';
        $this->autoLoader->addPsr4($namespace, $path);
    }

    /**
     * Instantiates a plugin
     *
     * @param $className
     * @return mixed
     */
    public function bootstrapPlugin($className)
    {
        $plugin = new $className($this->container);

        return $plugin;
    }

    /**
     * Inject the di container into the plugin
     *
     * @param ContainerBuilder $container
     */
    public function injectContainer(ContainerBuilder $container)
    {
        foreach ($this->plugins as $plugin) {
            if ($plugin instanceof ContainerAwarePlugin) {
                $plugin->setContainer($container);
            }
        }
    }

    /**
     * Return all plugins
     *
     * @return mixed
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Returns the plugin queried by name
     *
     * @param $name
     * @return mixed
     */
    public function getPlugin($name)
    {
        return $this->plugins[$name];
    }

    /**
     * Overwrite the instance of plugin $name with $class
     *
     * @param $name
     * @param $class
     */
    public function setPlugin($name, $class)
    {
        $this->plugins[$name] = $class;
    }

}
