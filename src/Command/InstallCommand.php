<?php

namespace ShopwareCli\Command;

use ShopwareCli\Services\Install;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Install a plugin
 *
 * Class InstallCommand
 * @package ShopwareCli\Command
 */
class InstallCommand extends BaseCommand
{
    /**
     * @var string
     */
    protected $shopwarePath;

    /**
     * @return Install
     */
    public function getInstallService()
    {
        return $this->container->get('install_service');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('plugin:install')
            ->setDescription('Install a plugin in the current or a given shopware installation')
            ->addArgument(
                'names',
                InputArgument::IS_ARRAY,
                'Name of the plugin to install'
            )
            ->addOption(
                'shopware-root',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Root of your shopware installation'
            )
            ->addOption(
                'small',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
            ->addOption(
                'useHttp',
                null,
                InputOption::VALUE_NONE,
                'Checkout the repo via HTTP'
            )
            ->addOption(
                'branch',
                '-b',
                InputOption::VALUE_OPTIONAL,
                'Checkout the given branch'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $names = $input->getArgument('names');
        $small = $input->getOption('small');
        $useHttp = $input->getOption('useHttp');
        $branch = $input->getOption('branch');
        $shopwarePath = $input->getOption('shopware-root');

        if (!$shopwarePath) {
            $shopwarePath = null;
        }
        $this->container->get('utilities')->cls();
        $shopwarePath = $this->container->get('utilities')->getValidShopwarePath($shopwarePath);
        $this->shopwarePath = $shopwarePath;

        $interactionManager = $this->container->get('plugin_operation_manager');

        $this->container->get('plugin_column_renderer')->setSmall($small);

        if (!empty($names)) {
            $params = array( 'activate' => $this->askActivatePluginQuestion(), 'useHttp' => $useHttp);
            $params['output'] = $output;
            $params['branch'] = $branch;
            $interactionManager->searchAndOperate($names, array($this, 'doInstall'), $params);

            return;
        }

        $interactionManager->operationLoop(array($this, 'doInstall'), array( 'branch' => $branch, 'useHttp' => $useHttp));
    }

    /**
     * @param $plugin
     * @param $params
     */
    public function doInstall($plugin, &$params)
    {
        if (!isset($params['activate'])) {
            $params['activate'] = $this->askActivatePluginQuestion();
        }
        $this->container->get('utilities')->changeDir($this->getShopwarePath() . '/engine/Shopware/Plugins/Local/');

        $this->getInstallService()->install($plugin, $this->getShopwarePath(), $params['activate'], $params['branch'], $params['useHttp']);

    }

    /**
     * @return mixed
     */
    protected function getShopwarePath()
    {
        $this->container->get('utilities')->changeDir($this->shopwarePath);

        return $this->shopwarePath;
    }

    /**
     * @return bool
     */
    protected function askActivatePluginQuestion()
    {
        $question = new ConfirmationQuestion('<question>Activate plugins after checkout?</question> <comment>[Y/n]</comment> ', true);
        $activate = $this->container->get('io_service')->ask($question);

        return $activate;
    }
}
