<?php

namespace ShopwareCli;

use Composer\Autoload\ClassLoader;
use ShopwareCli\Application\DependencyInjection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Main application of the cli tools
 *
 * Class Application
 * @package ShopwareCli
 */
class Application extends \Symfony\Component\Console\Application
{
    const NAME = 'sw-cli-tools';
    const VERSION = '@package_version@';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Composer\Autoload\ClassLoader
     */
    private $loader;

    /**
     * @param ClassLoader $loader
     */
    public function __construct(ClassLoader $loader)
    {
        $this->loader = $loader;

        parent::__construct(static::NAME, static::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->container = DependencyInjection::createContainer();

        $this->registerAutoLoader($this->loader);
        $this->container->get('plugin_manager')->init();

        $this->addCommands($this->container->get('command_manager')->getCommands());

        return parent::doRun($input, $output);
    }

    /**
     * Add the plugin path to the loader
     */
    private function registerAutoLoader(ClassLoader $loader)
    {
        $loader->addPsr4(
            __NAMESPACE__ . "\\Plugin\\",
            $this->container->get('path_provider')->getPluginPath()
        );
    }
}
