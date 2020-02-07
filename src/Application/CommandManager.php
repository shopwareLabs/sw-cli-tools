<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Application;

use ShopwareCli\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CommandManager
{
    /**
     * @var ContainerBuilder
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
     */
    public function getCommands(): array
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
     */
    public function collectPluginCommands(): array
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

    private function getDefaultCommands(): array
    {
        return [
            new Command\CacheCommand(),
            new Command\CacheGetCommand(),
        ];
    }
}
