<?php

namespace ShopwareCli\Application;

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

    public function __construct($pluginDirs, $container)
    {
        $this->pluginDirs = array_unique($pluginDirs);
        $this->container = $container;
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
            $folders = scandir($pluginDir);

            foreach ($folders as $folder) {
                // skip files and dot-folders
                if (!is_dir($pluginDir . '/' . $folder) ||strpos($folder, '.') === 0) {
                    continue;
                }
                if (!file_exists("{$pluginDir}/{$folder}/Bootstrap.php")) {
                    throw new \RuntimeException("Could not find Bootstrap.php in {$pluginDir}/{$folder}");
                }

                $className = "Plugin\\{$folder}\\Bootstrap";
                $this->setPlugin($folder, $this->bootstrapPlugin($className));
            }
        }
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