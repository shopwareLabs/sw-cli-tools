<?php
namespace Shopware\Install\Command;

use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShopwareInstallVcsCommand extends BaseCommand
{
    const MAIN_BRANCH = '5.2';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('install:vcs')
            ->setDescription('Allows setting up shopware from VCS.')
            ->addOption(
                'branch',
                '-b',
                InputOption::VALUE_OPTIONAL,
                'Branch to checkout'
            )
            ->addOption(
                'databaseName',
                '-d',
                InputOption::VALUE_OPTIONAL,
                'Name of database'
            )
            ->addOption(
                'installDir',
                'i',
                InputOption::VALUE_OPTIONAL,
                'Install directory'
            )
            ->addOption(
                'basePath',
                'p',
                InputOption::VALUE_OPTIONAL,
                'base path of the shop'
            )
            ->addOption(
                'user',
                'u',
                InputOption::VALUE_OPTIONAL,
                'GIT repos username. If given, checkout will be done using HTTP'
            )
            ->addOption(
                'noDemoData',
                null,
                InputOption::VALUE_NONE,
                'If set, the demo data will not be installed'
            )
            ->setHelp(
<<<EOF
The <info>%command.name%</info> sets up shopware
EOF
            );
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Shopware\Install\Services\Install\Vcs $installService */
        $installService = $this->container->get('shopware_vcs_install_service');

        $installService->installShopware(
            $input->getOption('branch'),
            trim($input->getOption('installDir'), '/'),
            $input->getOption('basePath'),
            $input->getOption('databaseName'),
            $input->getOption('user'),
            $input->getOption('noDemoData')
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var $ioService IoService */
        $ioService = $this->container->get('io_service');

        $branch = $this->askBranch($input, $ioService);

        $suggestion = $this->suggestNameFromBranch($branch) ?: self::MAIN_BRANCH;

        $installDir = $this->askInstallationDir($input, $ioService, $suggestion);

        $suggestion = $installDir ?: $suggestion;

        $this->askDatabaseName($input, $ioService, $suggestion);
        $this->askBasePath($input, $ioService, $suggestion);
    }

    /**
     * Try to guess a proper name (swTICKETNUMBER) from the branch name
     *
     * @param  string $branch
     * @return string
     */
    private function suggestNameFromBranch($branch)
    {
        $result = array();
        $pattern = '#sw-(?P<number>.+?)/(?P<target>.+?)/.*#i';
        preg_match($pattern, $branch, $result);

        if (isset($result['number'])) {
            return 'sw' . $result['number'];
        }

        return str_replace('.', '', $branch);
    }

    /**
     * @param  string            $path
     * @throws \RuntimeException
     * @return string
     */
    public function validateInstallDir($path)
    {
        if (is_dir($path)) {
            throw new \RuntimeException("Path '{$path}'' is not empty");
        }

        return $path;
    }

    /**
     * @param InputInterface $input
     * @param IoService      $ioService
     * @param string         $suggestion
     *
     * @return string
     */
    private function askInstallationDir(InputInterface $input, IoService $ioService, $suggestion)
    {
        $installDir = $input->getOption('installDir');
        if (!$installDir) {
            $installDir = $ioService->askAndValidate(
                "Please provide the install directory <{$suggestion}>: ",
                array($this, 'validateInstallDir')
            );
            $input->setOption('installDir', trim($installDir) ? $installDir : $suggestion);

            return $installDir;
        }

        return $installDir;
    }

    /**
     * @param InputInterface $input
     * @param IoService      $ioService
     * @param string         $suggestion
     */
    private function askDatabaseName(InputInterface $input, IoService $ioService, $suggestion)
    {
        $databaseName = $input->getOption('databaseName');
        if (!$databaseName) {
            $databaseName = $ioService->ask("Please provide the database name you want to use <{$suggestion}>: ");
            $input->setOption('databaseName', trim($databaseName) ? $databaseName : $suggestion);
        }
    }

    /**
     * @param InputInterface $input
     * @param IoService      $ioService
     * @param string         $suggestion
     */
    private function askBasePath(InputInterface $input, IoService $ioService, $suggestion)
    {
        $basePath = $input->getOption('basePath');
        if (!$basePath) {
            $basePath = $ioService->ask("Please provide the basepath you want to use <{$suggestion}>: ");
            $input->setOption('basePath', trim($basePath) ? $basePath : $suggestion);
        }
    }

    /**
     * @param InputInterface $input
     * @param IoService      $ioService
     *
     * @return string
     */
    private function askBranch(InputInterface $input, IoService $ioService)
    {
        $branch = $input->getOption('branch');
        if (!$branch) {
            $branchSuggestion = self::MAIN_BRANCH;
            $branch = $ioService->ask("Please provide the branch you want to install <{$branchSuggestion}>: ");
            $branch = trim($branch) ? $branch : self::MAIN_BRANCH;
            $input->setOption('branch', $branch);

            return $branch;
        }

        return $branch;
    }
}
