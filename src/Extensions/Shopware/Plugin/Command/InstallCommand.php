<?php

namespace Shopware\Plugin\Command;

use Shopware\Plugin\Services\ConsoleInteraction\PluginOperationManager;
use Shopware\Plugin\Struct\Plugin;
use ShopwareCli\Command\BaseCommand;
use Shopware\Plugin\Services\Install;
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
                'Name of the plugin to install, or the repository to filter by'
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
                'checkout',
                'c',
                InputOption::VALUE_NONE,
                'Checkout into current directory. No shopware checks, no subdirectory creation'
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
        $checkout = $input->getOption('checkout');

        if (!$shopwarePath) {
            $shopwarePath = null;
        }
        $this->container->get('io_service')->cls();

        if (!$checkout) {
            $shopwarePath = $this->container->get('utilities')->getValidShopwarePath($shopwarePath);
            $this->shopwarePath = $shopwarePath;
        }

        /** @var PluginOperationManager $interactionManager */
        $interactionManager = $this->container->get('plugin_operation_manager');

        $this->container->get('plugin_column_renderer')->setSmall($small);

        $params = ['checkout' => $checkout, 'branch' => $branch, 'useHttp' => $useHttp];

        if (!empty($names)) {
            if (!$checkout) {
                $params['activate'] = $this->askActivatePluginQuestion();
            }
            $interactionManager->searchAndOperate($names, [$this, 'doInstall'], $params);

            return;
        }

        $interactionManager->operationLoop([$this, 'doInstall'], $params);
    }

    /**
     * @param $plugin
     * @param $params
     */
    public function doInstall($plugin, &$params)
    {
        if ($params['checkout']) {
            $this->checkout($plugin, $params);
        } else {
            $this->install($plugin, $params);
        }
    }

    /**
     * @return string
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

    /**
     * @param $plugin
     * @param $params
     * @return mixed
     */
    private function install($plugin, &$params)
    {
        if (!isset($params['activate'])) {
            $params['activate'] = $this->askActivatePluginQuestion();
        }

        $this->container->get('utilities')->changeDir($this->getShopwarePath() . '/engine/Shopware/Plugins/Local/');

        $this->getInstallService()->install(
            $plugin,
            $this->getShopwarePath(),
            $params['activate'],
            $params['branch'],
            $params['useHttp']
        );
    }

    /**
     * @param $plugin Plugin
     * @param $params
     * @return mixed
     */
    private function checkout($plugin, &$params)
    {
        $url = $params['useHttp'] ? $plugin->cloneUrlHttp : $plugin->cloneUrlSsh;

        $destination = strtolower($plugin->module . '_' . $plugin->name);
        $path = realpath('.') . '/' . $destination;
        $this->container->get('io_service')->writeln($path);
        $this->container->get('io_service')->writeln("<info>Checking out $plugin->name to $path</info>");

        $repo        = escapeshellarg($url);
        $branch      = $params['branch'] ? escapeshellarg($params['branch']) : null;
        $destination = escapeshellarg('./' . $destination);

        $branchArg = $branch ? "-b {$branch}" : '';

        $this->container->get('git_util')->run(
            "clone --progress {$branchArg} {$repo} {$destination}"
        );
    }
}
