<?php

namespace ShopwareCli\Application;
use Composer\Autoload\ClassLoader;

/**
 * Implements a simple plugin system.
 *
 * Plugins can be created in the "plugins" directory. Any folder needs to have a "Bootstrap.php" file.
 *
 * Basically you will get injected the DI container builder into the bootstrap's constructor - at this
 * point you might want to add your own dependencies to the container.
 *
 * If you implement the ConsoleAwarePlugin interface, the PluginManager will call getConsoleCommands()
 * on your bootstrap and expect you to return a list of instantiated ConsoleCommands
 *
 * Class PluginManager
 * @package ShopwareCli\Application
 */
class PluginManager
{
    protected $plugins;
    protected $pluginDirs;
    /**
     * @var \Composer\Autoload\ClassLoader
     */
    private $autoLoader;

    public function __construct($pluginDirs, ClassLoader $autoLoader, $container)
    {
        $this->pluginDirs = array_unique($pluginDirs);
        $this->container = $container;
        $this->autoLoader = $autoLoader;
    }

    public function init()
    {
        $this->readPlugins();
    }

    /**
     * Read all available plugins
     *
     * @throws \RuntimeException
     */
    protected function readPlugins()
    {
        foreach ($this->pluginDirs as $pluginDir) {
            $vendorFolders = scandir($pluginDir);

            foreach ($vendorFolders as $vendorFolder) {
                // skip files and dot-folders
                if (!is_dir($pluginDir . '/' . $vendorFolder) ||strpos($vendorFolder, '.') === 0) {
                    continue;
                }

                $this->registerPluginNamespace("{$pluginDir}/{$vendorFolder}/", "{$vendorFolder}\\");

                $pluginFolders = scandir($pluginDir . '/' . $vendorFolder);
                foreach ($pluginFolders as $pluginFolder) {
                    // skip files and dot-folders
                    if (!is_dir($pluginDir . '/' . $vendorFolder . '/' . $pluginFolder) ||strpos($pluginFolder, '.') === 0) {
                        continue;
                    }

                    if (!file_exists("{$pluginDir}/{$vendorFolder}/{$pluginFolder}/Bootstrap.php")) {
                        throw new \RuntimeException("Could not find Bootstrap.php in {$pluginDir}/{$vendorFolder}/{$pluginFolder}");
                    }
                    $className = "{$vendorFolder}\\{$pluginFolder}\\Bootstrap";
                    $pluginInstance = $this->bootstrapPlugin($className);
                    $this->setPlugin("{$vendorFolder}\\{$pluginFolder}", $pluginInstance);
                }
            }
        }
    }

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
     * Returns a flat array of all plugin's console commands
     *
     * @return array
     */
    public function getConsoleCommands()
    {
        $commands = array();

        foreach ($this->plugins as $plugin) {
            if ($plugin instanceof ConsoleAwarePlugin) {
                foreach ($plugin->getConsoleCommands() as $command) {
                    $commands[] = $command;
                }
            }
        }

        return $commands;
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
