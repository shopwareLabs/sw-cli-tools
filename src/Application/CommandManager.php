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

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function getCommands()
    {
        $commands = array_merge(
            $this->getDefaultCommands(),
            $this->container->get('plugin_manager')->getConsoleCommands()
        );

        foreach ($commands as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($this->container);
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
