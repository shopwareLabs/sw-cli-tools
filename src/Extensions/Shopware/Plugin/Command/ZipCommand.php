<?php

namespace Shopware\Plugin\Command;

use ShopwareCli\Command\BaseCommand;
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
            ->setName('plugin:zip:vcs')
            ->setDescription('Creates a installable plugin zip in the current directory from VCS')
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

        $this->container->get('utilities')->cls();

        $this->container->get('plugin_column_renderer')->setSmall($small);
        $interactionManager = $this->container->get('plugin_operation_manager');

        $params = array('output' => $output, 'branch' => $branch, 'useHttp' => $useHttp);

        if (!empty($names)) {
            $interactionManager->searchAndOperate($names, array($this, 'doZip'), $params);

            return;
        }

        $interactionManager->operationLoop(array($this, 'doZip'), $params);

    }

    public function doZip($plugin, $params)
    {
        $this->container->get('zip_service')->zip($plugin, $this->getTempDir(), $this->getZipDir(), $params['branch'], $params['useHttp']);
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
