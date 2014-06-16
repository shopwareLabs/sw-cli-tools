<?php

namespace Shopware\RunCli;

use Shopware\RunCli\Command\RunCliCommand;
use ShopwareCli\Application\ConsoleAwarePlugin;

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
