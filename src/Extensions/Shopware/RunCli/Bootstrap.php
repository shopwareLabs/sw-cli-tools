<?php

namespace Shopware\RunCli;

use Shopware\RunCli\Command\RunCliCommand;
use ShopwareCli\Application\ConsoleAwareExtension;

class Bootstrap implements ConsoleAwareExtension
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
