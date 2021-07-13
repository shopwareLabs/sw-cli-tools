<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli;

use Composer\Autoload\ClassLoader;
use ShopwareCli\Application\DependencyInjection;
use ShopwareCli\Application\ExtensionManager;
use ShopwareCli\Services\PathProvider\PathProvider;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Main application of the cli tools
 */
class Application extends SymfonyApplication
{
    public const NAME = 'sw-cli-tools';
    public const VERSION = '__VERSION__';

    /**
     * @var ClassLoader
     */
    private $loader;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ClassLoader $loader)
    {
        $this->loader = $loader;

        parent::__construct(static::NAME, static::VERSION);

        $this->container = DependencyInjection::createContainer(\dirname(__DIR__));
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

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Add global "--no-extensions" option
     *
     * @return InputDefinition
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
     */
    protected function createContainer(InputInterface $input, OutputInterface $output): ContainerBuilder
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
    protected function checkDirectories(): void
    {
        /** @var PathProvider $pathProvider */
        $pathProvider = $this->container->get('path_provider');
        $paths = [
            $pathProvider->getAssetsPath(),
            $pathProvider->getCachePath(),
            $pathProvider->getExtensionPath(),
            $pathProvider->getConfigPath(),
        ];

        foreach ($paths as $dir) {
            if (\is_dir($dir)) {
                continue;
            }

            if (!\is_dir($dir) && !\mkdir($dir, 0777, true) && !\is_dir($dir)) {
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
    protected function loadExtensions($noExtensions): void
    {
        $paths = [$this->container->get('path_provider')->getCliToolPath() . '/src/Extensions'];

        if (!$noExtensions) {
            $paths[] = $this->container->get('path_provider')->getExtensionPath();
        }

        /** @var ExtensionManager $extensionManager */
        $extensionManager = $this->container->get('extension_manager');

        $extensionManager->discoverExtensions($paths);
        $extensionManager->injectContainer($this->container);
    }
}
