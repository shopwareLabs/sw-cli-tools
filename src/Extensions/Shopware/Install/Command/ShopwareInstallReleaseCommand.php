<?php
namespace Shopware\Install\Command;

use Shopware\Install\Struct\InstallationRequest;
use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShopwareInstallReleaseCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('install:release')
            ->setDescription('Allows setting up shopware from release package.')
            ->addOption(
                'release',
                '-r',
                InputOption::VALUE_REQUIRED,
                'Release version. Default: Latest'
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
                'username',
                null,
                InputOption::VALUE_REQUIRED,
                'User name of the backend user'
            )
            ->addOption(
                'password',
                null,
                InputOption::VALUE_REQUIRED,
                'password name of the backend user'
            )
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'Full name of the backend user'
            )
            ->addOption(
                'mail',
                null,
                InputOption::VALUE_REQUIRED,
                'email of the backend user'
            )
            ->addOption(
                'language',
                null,
                InputOption::VALUE_OPTIONAL,
                'Language of the backend user. Currently only de_DE and en_GB are supported',
                'de_DE'
            )
            ->setHelp(
<<<EOF
            The <info>%command.name%</info> sets up shopware
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new InstallationRequest(array(
            'username'     => $input->getOption('username'),
            'password'     => $input->getOption('password'),
            'name'         => $input->getOption('name'),
            'mail'         => $input->getOption('mail'),
            'language'     => $input->getOption('language'),
            'release'      => $input->getOption('release'),
            'installDir'   => trim($input->getOption('installDir'), '/'),
            'basePath'     => $input->getOption('basePath'),
            'databaseName' => $input->getOption('databaseName')
        ));

        /** @var \Shopware\Install\Services\Install\Release $installService */
        $installService = $this->container->get('shopware_release_install_service');
        $installService->installShopware($request);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function interact(InputInterface $input, OutputInterface $output)
    {
        $this->validateInput($input);

        /** @var $ioService IoService */
        $ioService = $this->container->get('io_service');

        $this->askGenericOptions($input, $ioService);

        $release = $this->askRelease($input, $ioService);

        $suggestion = $release ?: 'latest';

        $installDir = $this->askInstallationDirectory($input, $ioService, $suggestion);

        $suggestion = $installDir ?: $suggestion;

        $this->askDatabaseName($input, $ioService, $suggestion);

        $this->askBasePath($input, $ioService, $suggestion);
    }

    /**
     * @param  string            $input
     * @return string
     * @throws \RuntimeException
     */
    public function genericValidator($input)
    {
        if (empty($input)) {
            throw new \RuntimeException("Field may not be empty");
        }

        return $input;
    }

    /**
     * @param  string            $path
     * @return string
     * @throws \RuntimeException
     */
    public function validateInstallDir($path)
    {
        if (is_dir($path)) {
            throw new \RuntimeException("Path '{$path}'' is not empty");
        }

        return $path;
    }

    /**
     * @param  InputInterface    $input
     * @throws \RuntimeException
     */
    protected function validateInput(InputInterface $input)
    {
        $language = $input->getOption('language');
        if (!in_array($language, array('en_GB', 'de_DE'))) {
            throw new \RuntimeException("Invalid language: '$language'");
        }
    }

    /**
     * @param InputInterface $input
     * @param IoService      $ioService
     */
    private function askGenericOptions(InputInterface $input, IoService $ioService)
    {
        $required = array(
            'username' => 'backend user name',
            'password' => 'backend user password',
            'name' => 'your full name',
            'mail' => 'your email'
        );

        foreach ($required as $field => $description) {
            $fieldData = $input->getOption($field);
            if ($fieldData) {
                continue;
            }

            $fieldData = $ioService->askAndValidate(
                "Please enter $description: ",
                array($this, 'genericValidator')
            );
            $input->setOption($field, $fieldData);
        }
    }

    /**
     * @param InputInterface $input
     * @param IoService      $ioService
     *
     * @return string
     */
    private function askRelease(InputInterface $input, IoService $ioService)
    {
        $release = $input->getOption('release');
        if (!$release) {
            $release = $ioService->ask('Please provide the release you want to install <latest>: ');
            $release = trim($release) ? $release : 'latest';
            $input->setOption('release', $release);

            return $release;
        }

        return $release;
    }

    /**
     * @param InputInterface $input
     * @param IoService      $ioService
     * @param string         $suggestion
     *
     * @return string
     */
    private function askInstallationDirectory(InputInterface $input, IoService $ioService, $suggestion)
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
}
