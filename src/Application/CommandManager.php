<?php

namespace ShopwareCli\Application;

use ShopwareCli\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CommandManager
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;
    /**
     * @var ExtensionManager
     */
    private $extensionManager;

    public function __construct(ExtensionManager $extensionManager, ContainerBuilder $container)
    {
        $this->extensionManager = $extensionManager;
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
        $commands = [];

        foreach ($this->extensionManager->getExtensions() as $plugin) {
            if ($plugin instanceof ConsoleAwareExtension) {
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
        return [
            new Command\CacheCommand(),
            new Command\CacheGetCommand()
        ];
    }
}
