<?php

namespace ShopwareCli\Application;

interface ConsoleAwarePlugin
{
    /**
     * Return an array with instances of your console commands here
     *
     * @return mixed
     */
    public function getConsoleCommands();
}
