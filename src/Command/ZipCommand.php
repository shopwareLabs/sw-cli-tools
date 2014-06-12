<?php

namespace ShopwareCli\Command;

use ShopwareCli\Application\DependencyInjection;
use ShopwareCli\Command\Helpers\PluginOperationManager;
use ShopwareCli\Command\Helpers\PluginInputVerificator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Zip a plugin
 *
 * Class ZipCommand
 * @package ShopwareCli\Command
 */
class ZipCommand extends BaseCommand
{
    protected $utilities;
    protected $zipDir;

    protected function configure()
    {
        $this
            ->setName('plugin:zip')
            ->setDescription('Creates a installable plugin zip in the current directory')
            ->addArgument(
                'names',
                InputArgument::IS_ARRAY,
                'Name of the plugin to install'
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

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->zipDir = getcwd();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $names = $input->getArgument('names');
        $small = $input->getOption('small');
        $useHttp = $input->getOption('useHttp');
        $branch = $input->getOption('branch');

        /** @var DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');

        /** @var $questionHelper QuestionHelper */
        $questionHelper = $this->getHelperSet()->get('question');

        $this->container->get('utilities')->cls();

        $pluginManager = $this->container->get('manager_factory')->factory($useHttp);

        $pluginSelector = new PluginInputVerificator($input, $output, $questionHelper, $this->container->get('config'), $small);

        $interactionManager = new PluginOperationManager($pluginManager, $pluginSelector, $dialog, $output, $this->container->get('utilities'));

        $params = array('output' => $output, 'branch' => $branch);

        if (!empty($names)) {
            $interactionManager->searchAndOperate($names, array($this, 'doZip'), $params);

            return;
        }

        $interactionManager->operationLoop(array($this, 'doZip'), $params);

    }

    public function doZip($plugin, $params)
    {
        $this->container->get('zip_service')->zip($plugin, $this->getTempDir(), $this->getZipDir(), $params['branch']);
    }

    protected function getTempDir()
    {
        $tempDirectory = sys_get_temp_dir();
        $tempDirectory .= '/plugin-inst-' . uniqid();
        mkdir($tempDirectory, 0777, true);

        $this->container->get('utilities')->changeDir($tempDirectory);

        return $tempDirectory;
    }

    protected function getZipDir()
    {
        return $this->zipDir;
    }

}
