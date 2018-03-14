<?php

namespace ShopwareCli;

use Composer\Autoload\ClassLoader;
use ShopwareCli\Application\DependencyInjection;
use ShopwareCli\Services\PathProvider\PathProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

        $this->container = DependencyInjection::createContainer(dirname(__DIR__));
    }

    /**
     * {@inheritdoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->createContainer($input, $output);
        $this->checkDirectories();

        $noExtensions = $input->hasParameterOption('--no-extensions');
        $this->loadExtensions($noExtensions);

        // Compile the container after the plugins did their container extensions
        $this->container->compile();

        $this->addCommands($this->container->get('command_manager')->getCommands());

        $this->container->get('plugin_provider')->setRepositories($this->container->get('repository_manager')->getRepositories());

        return parent::doRun($input, $output);
    }

    /**
     * Add global "--no-extensions" option
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        $inputDefinitions = parent::getDefaultInputDefinition();
        $inputDefinitions->addOption(
            new InputOption('--no-extensions', null, InputOption::VALUE_NONE, 'Don\'t load 3rd party extensions.')
        );

        return $inputDefinitions;
    }

    /**
     * Creates the container and sets some services which are only synthetic in the container
     *
     * @param  InputInterface   $input
     * @param  OutputInterface  $output
     * @return ContainerBuilder
     */
    protected function createContainer(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getHelperSet()->get('question');

        $this->container->set('output_interface', $output);
        $this->container->set('input_interface', $input);
        $this->container->set('question_helper', $questionHelper);
        $this->container->set('helper_set', $this->getHelperSet());
        $this->container->set('autoloader', $this->loader);

        return $this->container;
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

        foreach ([
            $pathProvider->getAssetsPath(),
            $pathProvider->getCachePath(),
            $pathProvider->getExtensionPath(),
            $pathProvider->getConfigPath()
         ] as $dir) {
            if (is_dir($dir)) {
                continue;
            }

            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \RuntimeException("Could not find / create $dir");
            }
        }
    }

    /**
     * Load extensions. The default extensions are always loaded,
     * 3rd party extensions depending on $noExtensions
     *
     * Default extensions are loaded first
     *
     * @param bool $noExtensions
     */
    protected function loadExtensions($noExtensions)
    {
        $paths = [$this->container->get('path_provider')->getCliToolPath() . '/src/Extensions'];

        if (!$noExtensions) {
            $paths[] = $this->container->get('path_provider')->getExtensionPath();
        }

        $this->container->get('extension_manager')->discoverExtensions($paths);
        $this->container->get('extension_manager')->injectContainer($this->container);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
