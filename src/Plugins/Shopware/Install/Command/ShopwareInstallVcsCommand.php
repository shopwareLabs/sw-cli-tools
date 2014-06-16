<?php
namespace Shopware\Install\Command;

use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShopwareInstallVcsCommand extends BaseCommand
{
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
            $input->getOption('user')
        );
    }

    public function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var $ioService IoService */
        $ioService = $this->container->get('io_service');

        $branch = $input->getOption('branch');
        if (!$branch) {
            $branch = $ioService->ask('Please provide the branch you want to install <master>: ');
            $branch = trim($branch) ? $branch : 'master';
            $input->setOption('branch', $branch);
        }

        $suggestion = $this->suggestNameFromBranch($branch) ?: 'master';

        $installDir = $input->getOption('installDir');
        if (!$installDir) {
            $installDir = $ioService->askAndValidate("Please provide the install directory <{$suggestion}>: ", array($this,'validateInstallDir'));
            $input->setOption('installDir', trim($installDir) ? $installDir : $suggestion);
        }

        $suggestion = $installDir ?: $suggestion;

        $databaseName = $input->getOption('databaseName');
        if (!$databaseName) {
            $databaseName = $ioService->ask("Please provide the database name you want to use <{$suggestion}>: ");
            $input->setOption('databaseName', trim($databaseName) ? $databaseName : $suggestion);
        }

        $basePath = $input->getOption('basePath');
        if (!$basePath) {
            $basePath = $ioService->ask("Please provide the basepath you want to use <{$suggestion}>: ");
            $input->setOption('basePath', trim($basePath) ? $basePath : $suggestion);
        }
    }

    /**
     * Try to guess a proper name (swTICKETNUMBER) from the branch name
     *
     * @param string $branch
     * @return string
     */
    private function suggestNameFromBranch($branch)
    {
        $result = array();
        $pattern = '#(?P<type>.+?)/(?P<target>.+?)/sw-(?P<number>.+)-.*#i';
        preg_match($pattern, $branch, $result);

        if (isset($result['number'])) {
            return 'SW' . $result['number'];
        }

        return $branch;
    }

    /**
     * @param string $path
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
}
