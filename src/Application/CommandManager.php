<?php

namespace ShopwareCli\Application;

use ShopwareCli\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class CommandManager
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;
    /**
     * @var PluginManager
     */
    private $pluginManager;

    public function __construct(PluginManager $pluginManager, ContainerBuilder $container)
    {
        $this->pluginManager = $pluginManager;
        $this->container = $container;
    }

    /**
     * Returns all commands
     *
     * @return array
     */
    public function getCommands()
    {
        $commands = array_merge(
            $this->getDefaultCommands(),
            $this->collectPluginCommands()
        );

        foreach ($commands as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($this->container);
            }
        }

        return $commands;
    }

    /**
     * Returns a flat array of all plugin's console commands
     *
     * @return array
     */
    public function collectPluginCommands()
    {
        $commands = array();

        foreach ($this->pluginManager->getPlugins() as $plugin) {
            if ($plugin instanceof ConsoleAwarePlugin) {
                foreach ($plugin->getConsoleCommands() as $command) {
                    $commands[] = $command;
                }
            }
        }

        return $commands;
    }

    /**
     * @return array
     */
    private function getDefaultCommands()
    {
        return array(
            new Command\InstallCommand(),
            new Command\ZipCommand(),
            new Command\CacheCommand(),
            new Command\CacheGetCommand()
        );
    }
}
