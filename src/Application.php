<?php

namespace ShopwareCli;

use Composer\Autoload\ClassLoader;
use ShopwareCli\Application\DependencyInjection;
use ShopwareCli\Services\PathProvider\PathProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
     * @var \Composer\Autoload\ClassLoader
     */
    private $loader;

    /**
     * @var ContainerInterface
     */
    private $container;

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
        $this->checkDirectories();

        $ignore3rdPartyPlugins = $input->hasParameterOption('--no-plugins');
        $this->loadPlugins($ignore3rdPartyPlugins);

        $this->addCommands($container->get('command_manager')->getCommands());
        $container->get('plugin_provider')->setRepositories($container->get('repository_manager')->getRepositories());

        return parent::doRun($input, $output);
    }

    /**
     * Add global "--no-plugins" option
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        $inputDefinitions = parent::getDefaultInputDefinition();
        $inputDefinitions->addOption(
            new InputOption('--no-plugins', null, InputOption::VALUE_NONE, 'Don\'t load 3rd party plugins.')
        );

        return $inputDefinitions;
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

        return $this->container = $container;
    }

    /**
     * Make sure, that the required directories do actually exist
     *
     * @throws \RuntimeException
     */
    protected function checkDirectories()
    {
        /** @var  $pathProvider PathProvider */
        $pathProvider = $this->container->get('path_provider');

        foreach (array(
            $pathProvider->getAssetsPath(),
            $pathProvider->getCachePath(),
            $pathProvider->getPluginPath(),
            $pathProvider->getConfigPath()
         ) as $dir) {
            if (is_dir($dir)) {
                continue;
            }

            if (!@mkdir($dir, 0777, true)) {
                throw new \RuntimeException("Could not find / create $dir");
            }
        }
    }

    /**
     * Load plugins. The default plugins are always loaded, 3rd party plugins depending on $ignore3rdPartyPlugins
     *
     * Default plugins are loaded first
     *
     * @param $ignore3rdPartyPlugins
     */
    protected function loadPlugins($ignore3rdPartyPlugins)
    {
        $paths = array($this->container->get('path_provider')->getCliToolPath() . '/src/Plugins');

        if (!$ignore3rdPartyPlugins) {
            $paths[] = $this->container->get('path_provider')->getPluginPath();
        }

        $this->container->get('plugin_manager')->discoverPlugins($paths);
        $this->container->get('plugin_manager')->injectContainer($this->container);
    }
}
