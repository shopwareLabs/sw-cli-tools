<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Command;

use Shopware\Plugin\Services\ConsoleInteraction\PluginColumnRenderer;
use Shopware\Plugin\Services\ConsoleInteraction\PluginOperationManager;
use Shopware\Plugin\Services\Install;
use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Services\GitUtil;
use ShopwareCli\Services\IoService;
use ShopwareCli\Utilities;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Install a plugin
 */
class InstallCommand extends BaseCommand
{
    /**
     * @var string
     */
    private $shopwarePath;

    public function doInstall($plugin, &$params): void
    {
        if ($params['checkout']) {
            $this->checkout($plugin, $params);
        } else {
            $this->install($plugin, $params);
        }
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
        $this->getIOService()->cls();

        if (!$checkout) {
            $shopwarePath = $this->getUtilities()->getValidShopwarePath($shopwarePath);
            $this->shopwarePath = $shopwarePath;
        }

        /** @var PluginOperationManager $interactionManager */
        $interactionManager = $this->container->get('plugin_operation_manager');

        /** @var PluginColumnRenderer $pluginColumnRenderer */
        $pluginColumnRenderer = $this->container->get('plugin_column_renderer');
        $pluginColumnRenderer->setSmall($small);

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

    private function getShopwarePath(): string
    {
        $this->getUtilities()->changeDir($this->shopwarePath);

        return $this->shopwarePath;
    }

    private function askActivatePluginQuestion(): bool
    {
        $question = new ConfirmationQuestion(
            '<question>Activate plugins after checkout?</question> <comment>[Y/n]</comment> ',
            true
        );

        return $this->getIOService()->ask($question);
    }

    private function install($plugin, &$params): void
    {
        if (!isset($params['activate'])) {
            $params['activate'] = $this->askActivatePluginQuestion();
        }

        /** @var Install $installService */
        $installService = $this->container->get('install_service');
        $installService->install(
            $plugin,
            $this->getShopwarePath(),
            $params['activate'],
            $params['branch'],
            $params['useHttp']
        );
    }

    /**
     * @param mixed $plugin Plugin
     */
    private function checkout($plugin, &$params): void
    {
        $url = $params['useHttp'] ? $plugin->cloneUrlHttp : $plugin->cloneUrlSsh;

        $destination = strtolower($plugin->module . '_' . $plugin->name);
        $path = realpath('.') . '/' . $destination;
        $this->getIOService()->writeln("<info>Checking out $plugin->name to $path</info>");

        $repo = escapeshellarg($url);
        $branch = $params['branch'] ? escapeshellarg($params['branch']) : null;
        $destination = escapeshellarg('./' . $destination);

        $branchArg = $branch ? "-b {$branch}" : '';

        /** @var GitUtil $gitUtil */
        $gitUtil = $this->container->get('git_util');
        $gitUtil->run(
            "clone --progress {$branchArg} {$repo} {$destination}"
        );
    }

    private function getIOService(): IoService
    {
        return $this->container->get('io_service');
    }

    private function getUtilities(): Utilities
    {
        return $this->container->get('utilities');
    }
}
