<?php

namespace Shopware\RunCli;

use Shopware\RunCli\Command\RunCliCommand;
use ShopwareCli\Application\ConsoleAwarePlugin;
use ShopwareCli\Application\ContainerAwarePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Bootstrap implements ConsoleAwarePlugin
{
    /**
     * {@inheritdoc}
     */
    public function getConsoleCommands()
    {
        return array(
            new RunCliCommand()
        );
    }
}
