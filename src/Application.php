<?php

namespace ShopwareCli;

use Composer\Autoload\ClassLoader;
use ShopwareCli\Application\DependencyInjection;
use ShopwareCli\Services\PathProvider\PathProvider;
use ShopwareCli\Application\PluginManager;
use ShopwareCli\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Main application of the cli tools
 *
 * Class Application
 * @package ShopwareCli
 */
class Application extends \Symfony\Component\Console\Application
{
    protected $container;

    /**
     * Add the plugin path from the plugin XDG dir to the loader
     */
    private function registerAutoLoader(ClassLoader $loader)
    {
        $loader->addPsr4("Plugin\\", $this->container->get('path_provider')->getPluginPath());
    }

    public function setup(ClassLoader $loader)
    {
        $this->container = DependencyInjection::createContainer();

        $this->registerAutoLoader($loader);

        $this->container->get('plugin_manager')->init();

        $this->addCommands($this->container->get('command_manager')->getCommands());
    }
}
