<?php

namespace ShopwareCli;

use Composer\Autoload\ClassLoader;
use ShopwareCli\Application\DependencyInjection;
use ShopwareCli\OutputWriter\WrappedOutputWriter;
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

        parent::__construct('sw-cli-tools', '@package_version@');
    }

    /**
     * {@inheritdoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->setupContainer($output);

        $this->container->get('plugin_manager')->init();

        $this->addCommands($this->container->get('command_manager')->getCommands());

        return parent::doRun($input, $output);
    }

    /**
     * Creates the container and sets some services which are only synthetic in the container
     *
     * @param OutputInterface $output
     */
    protected function setupContainer(OutputInterface $output)
    {
        $this->container = DependencyInjection::createContainer();

        $this->container->set('autoloader', $this->loader);
        $this->container->set('output_writer', new WrappedOutputWriter(array($output, 'writeln')));
    }
}
