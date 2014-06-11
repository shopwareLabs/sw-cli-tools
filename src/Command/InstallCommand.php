<?php

namespace ShopwareCli\Command;

use ShopwareCli\Application\DependencyInjection;
use ShopwareCli\Command\Helpers\PluginOperationManager;
use ShopwareCli\Command\Helpers\PluginInputVerificator;
use ShopwareCli\OutputWriter\OutputWriterInterface;
use ShopwareCli\OutputWriter\WrappedOutputWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstallCommand extends BaseCommand
{
    protected $shopwarePath;

    public function getInstallService()
    {
        return $this->container->get('install_service');
    }

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $names = $input->getArgument('names');
        $small = $input->getOption('small');
        $useHttp = $input->getOption('useHttp');
        $branch = $input->getOption('branch');
        $shopwarePath = $input->getOption('shopware-root');

        $pluginManager = $this->container->get('manager_factory')->factory($useHttp);

        /** @var DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');

        if (!$shopwarePath) {
            $shopwarePath = null;
        }
        $this->container->get('utilities')->cls();
        $shopwarePath = $this->container->get('utilities')->getValidShopwarePath($shopwarePath, $output, $dialog);
        $this->shopwarePath = $shopwarePath;

        $pluginSelector = new PluginInputVerificator($input, $output, $dialog, $this->container->get('config'), $small);

        $interactionManager = new PluginOperationManager($pluginManager, $pluginSelector, $dialog, $output, $this->container->get('utilities'));

        if (!empty($names)) {
            $params = array( 'activate' => $this->askActivatePluginQuestion($dialog, $output));
            $params['output'] = $output;
            $params['branch'] = $branch;
            $interactionManager->searchAndOperate($names, array($this, 'doInstall'), $params);
            return;
        }

        $interactionManager->operationLoop(array($this, 'doInstall'), array('output' => $output, 'branch' => $branch));
    }

    public function doInstall($plugin, &$params)
    {
        if (!isset($params['activate'])) {
            $params['activate'] = $this->askActivatePluginQuestion($this->getHelperSet()->get('dialog'), $params['output']);
        }

        $this->container->get('utilities')->changeDir($this->getShopwarePath() . '/engine/Shopware/Plugins/Local/');
        $this->getInstallService()->install($plugin, $this->getShopwarePath(), $params['activate'], $params['branch']);

    }


    protected function getShopwarePath()
    {
        $this->container->get('utilities')->changeDir($this->shopwarePath);
        return $this->shopwarePath;
    }

    protected function askActivatePluginQuestion(DialogHelper $dialog, $output)
    {
        $activate = $dialog->askConfirmation($output, '<question>Activate plugins after checkout?</question> <comment>[Y/n]</comment> ', true);
        return $activate;
    }

}