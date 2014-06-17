<?php

namespace ShopwareCli\Application;

use Symfony\Component\Console\Command\Command;

interface ConsoleAwareExtension
{
    /**
     * Return an array with instances of your console commands here
     *
     * @return Command[]
     */
    public function getConsoleCommands();
}
