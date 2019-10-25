<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Command;

use Shopware\PluginCreator\Services\GeneratorFactory;
use Shopware\PluginCreator\Struct\Configuration;
use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Config;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreatePluginCommand extends BaseCommand
{
    const LEGACY_OPTION = 'legacy';

    public function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
        $helper = $this->getHelperSet()->get('question');

        $name = $input->getArgument('name');
        $modelName = implode('', array_slice($this->upperToArray($name), 1));

        if ($input->getOption(self::LEGACY_OPTION)) {
            $defaultModel = sprintf('Shopware\CustomModels\%s\%s', $name, $modelName);
        } else {
            $defaultModel = sprintf('%s\Models\%s', $name, $modelName);
        }

        $this->normalizeBooleanFields($input);

        $backendModel = $input->getOption('backendModel');

        // for backend / api the backendModel is mandatory
        if (($input->getOption('haveBackend') || $input->getOption('haveApi')) && empty($backendModel)) {
            $question = new Question('<question>Please specify the main model for your backend application:</question> <comment>' . $defaultModel . '</comment>: ', $defaultModel);
            $question->setValidator($this->validateModel());
            $modelName = $helper->ask($input, $output, $question);
            $input->setOption('backendModel', $modelName);
        }

        // a backend implicitly sets "haveModel" to true, if the backend model is not a default model
        if ($input->getOption('haveBackend')
            && strpos($input->getOption('backendModel'), 'Shopware\Models') === false
        ) {
            $input->setOption('haveModels', true);
        }
    }

    /**
     * Make sure, that our booleans are actual booleans
     *
     * @param InputInterface $input
     */
    public function normalizeBooleanFields(InputInterface $input)
    {
        $inputOptions = [
            'haveBackend',
            'haveFrontend',
            'haveModels',
            'haveCommands',
            'haveWidget',
            'haveApi',
            'haveFilter',
            self::LEGACY_OPTION,
        ];

        foreach ($inputOptions as $key) {
            switch (strtolower($input->getOption($key))) {
                case 'false':
                case '0':
                    $input->setOption($key, false);
                    break;
                case 'true':
                case '1':
                    $input->setOption($key, true);
                    break;
            }
        }
    }

    /**
     * Split "SwagTestPlugin" into array("Swag", "Test", "Plugin")
     *
     * @param $input
     *
     * @return array
     */
    public function upperToArray($input)
    {
        return preg_split('/(?=[A-Z])/', $input, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Make sure the namespace is one of core, backend, frontend
     *
     * @param $input
     *
     * @throws \InvalidArgumentException
     *
     * @return $input
     */
    public function validateNamespace($input)
    {
        if (!in_array(strtolower($input), ['frontend', 'core', 'backend'])) {
            throw new \InvalidArgumentException('Namespace mus be one of FRONTEND, BACKEND or CORE');
        }

        return $input;
    }

    /**
     * Check the entered model (check might be somewhat more sufisticated)
     *
     * @throws \InvalidArgumentException
     */
    public function validateModel()
    {
        return function ($input) {
            if (empty($input)) {
                throw new \InvalidArgumentException('You need to enter a model name like »Shopware\Models\Article\Article«');
            }

            return $input;
        };
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->container->get('config');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('plugin:create')->setDescription('Creates a new plugin.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Prefixed name of the plugin to create. E.g: SwagAdvancedBasket'
            )
            ->addOption(
                self::LEGACY_OPTION,
                null,
                InputOption::VALUE_NONE,
                'Create a legacy Plugin for Shopware versions lower than 5.2'
            )
            ->addOption(
                'namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'Namespace to create the plugin in. One of Frontend, Core or Backend',
                'Frontend'
            )
            ->addOption(
                'haveBackend',
                'b',
                InputOption::VALUE_NONE,
                'Do you want a backend application to be created? This will create the ExtJS structure and connect it to an existing or new model'
            )
            ->addOption(
                'haveFilter',
                null,
                InputOption::VALUE_NONE,
                'Do you want to generate front filters / facets / conditions?'
            )
            ->addOption(
                'haveFrontend',
                'f',
                InputOption::VALUE_NONE,
                'Do you need a frontend controller?'
            )
            ->addOption(
                'backendModel',
                null,
                InputOption::VALUE_OPTIONAL,
                'If you need a backend application: What\'s the name of its main model?'
            )
            ->addOption(
                'haveModels',
                'm',
                InputOption::VALUE_NONE,
                'Do you want custom models to be created and registered?'
            )
            ->addOption(
                'haveCommands',
                'c',
                InputOption::VALUE_NONE,
                'Do you want your plugin to be prepared for commands?'
            )
            ->addOption(
                'haveWidget',
                'w',
                InputOption::VALUE_NONE,
                'Do you want your plugin to have a widget?'
            )
            ->addOption(
                'haveApi',
                'a',
                InputOption::VALUE_NONE,
                'Do you want your plugin to have an api resource?'
            )
            ->addOption(
                'haveElasticSearch',
                'e',
                InputOption::VALUE_NONE,
                'Do you want your plugin to have an elastic search integration?'
            )
            ->addOption(
                'licenseHeader',
                null,
                InputOption::VALUE_OPTIONAL,
                'File with your desired license header',
                ''
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> creates a new plugin.
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validateName($input->getArgument('name'));

        if ($input->getOption(self::LEGACY_OPTION)) {
            $this->validateNamespace($input->getOption('namespace'));
        }

        $configuration = $this->getConfigurationObject($input);
        $configuration->pluginConfig = $this->getConfig()->offsetGet('PluginConfig');

        $generator = (new GeneratorFactory())->create($configuration);

        $generator->run();
    }

    /**
     * Check the plugin name - it needs to constist of two parts at least - the first one is the dev prefix
     *
     * @param $name
     *
     * @throws \InvalidArgumentException
     */
    protected function validateName($name)
    {
        $parts = $this->upperToArray($name);
        if (count($parts) <= 1) {
            throw new \InvalidArgumentException('Name must be in CamelCase and have at least two components. Don\'t forget you developer-prefix');
        }
    }

    /**
     * Populate a configuration object by the input interface
     *
     * @param InputInterface $input
     *
     * @return Configuration
     */
    protected function getConfigurationObject(InputInterface $input)
    {
        $configuration = new Configuration();
        $configuration->name = $input->getArgument('name');
        $configuration->namespace = $input->getOption('namespace');
        $configuration->hasFrontend = $input->getOption('haveFrontend');
        $configuration->hasFilter = $input->getOption('haveFilter');
        $configuration->hasBackend = $input->getOption('haveBackend');
        $configuration->hasWidget = $input->getOption('haveWidget');
        $configuration->hasApi = $input->getOption('haveApi');
        $configuration->hasModels = $input->getOption('haveModels');
        $configuration->hasCommands = $input->getOption('haveCommands');
        $configuration->backendModel = $input->getOption('backendModel');
        $configuration->hasElasticSearch = $input->getOption('haveElasticSearch');
        $configuration->isLegacyPlugin = $input->getOption(self::LEGACY_OPTION);

        $licenseHeader = $input->getOption('licenseHeader');
        if (!empty($licenseHeader) && file_exists($licenseHeader)) {
            $configuration->licenseHeaderPlain = file_get_contents($licenseHeader);
            $configuration->licenseHeader = $this->prepareLicenseHeader($configuration->licenseHeaderPlain);
        } else {
            $configuration->licenseHeaderPlain = null;
            $configuration->licenseHeader = null;
        }

        return $configuration;
    }

    private function prepareLicenseHeader($license)
    {
        $license = str_replace("\n", "\n * ", trim($license));

        return "/**\n * " . $license . "\n */\n";
    }
}
