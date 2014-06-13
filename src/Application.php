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
    const NAME    = 'sw-cli-tools';
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
        $container = $this->createContainer($input, $output);

        $container->get('plugin_manager')->init();
        $this->addCommands($container->get('command_manager')->getCommands());

        return parent::doRun($input, $output);
    }

    /**
     * Creates the container and sets some services which are only synthetic in the container
     *
     * @param  InputInterface     $input
     * @param  OutputInterface    $output
     * @return ContainerInterface
     */
    protected function createContainer(InputInterface $input, OutputInterface $output)
    {
        $container = DependencyInjection::createContainer();

        $questionHelper = $this->getHelperSet()->get('question');

        $container->set('output_interface', $output);
        $container->set('input_interface', $input);
        $container->set('question_helper', $questionHelper);
        $container->set('autoloader', $this->loader);

        return $container;
    }
}
