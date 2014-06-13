<?php

namespace ShopwareCli;

use Composer\Autoload\ClassLoader;
use ShopwareCli\Application\DependencyInjection;
use ShopwareCli\Services\IoService;
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
        $this->setupContainer($input, $output);

        $this->container->get('plugin_manager')->init();

        $this->addCommands($this->container->get('command_manager')->getCommands());

        return parent::doRun($input, $output);
    }

    /**
     * Creates the container and sets some services which are only synthetic in the container
     *
     */
    protected function setupContainer(InputInterface $input, OutputInterface $output)
    {
        $this->container = DependencyInjection::createContainer();

        $this->container->set('autoloader', $this->loader);
        $this->container->set('io_service', new IoService($input, $output, $this->getHelperSet()));
    }
}
