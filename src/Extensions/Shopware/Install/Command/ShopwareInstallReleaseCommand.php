<?php
namespace Shopware\Install\Command;

use Shopware\Install\Struct\InstallationRequest;
use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Config;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShopwareInstallReleaseCommand extends BaseCommand
{
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
        $this->setName('install:release')
            ->setDescription('Allows setting up shopware from release package.')
            ->setHelp(
                <<<EOF
                            The <info>%command.name%</info> sets up shopware
EOF
            );

        $this->addInstallerOptions();
        $this->addDbOptions();
        $this->addShopOptions();
        $this->addAdminOptions();
    }

    private function addInstallerOptions()
    {
        $this
            ->addOption('release', 'r', InputOption::VALUE_REQUIRED, 'Release version. Default: Latest')
            ->addOption('install-dir', 'i', InputOption::VALUE_REQUIRED, 'Install directory');
    }

    private function addDbOptions()
    {
        $this
            ->addOption('db-host', null, InputOption::VALUE_REQUIRED, 'Database host', 'localhost')
            ->addOption('db-port', null, InputOption::VALUE_REQUIRED, 'Database port', '3306')
            ->addOption('db-socket', null, InputOption::VALUE_REQUIRED, 'Database socket')
            ->addOption('db-user', null, InputOption::VALUE_REQUIRED, 'Database user')
            ->addOption('db-password', null, InputOption::VALUE_REQUIRED, 'Database password')
            ->addOption('db-name', null, InputOption::VALUE_REQUIRED, 'Database name')
            ->addOption('no-skip-import', null, InputOption::VALUE_NONE, 'Import database data even if a valid schema already exists')
        ;
    }

    private function addShopOptions()
    {
        $this
            ->addOption('shop-locale', null, InputOption::VALUE_REQUIRED, 'Shop locale', 'de_DE')
            ->addOption('shop-host', null, InputOption::VALUE_REQUIRED, 'Shop host', 'localhost')
            ->addOption('shop-path', 'p', InputOption::VALUE_REQUIRED, 'Shop path', '')
            ->addOption('shop-name', null, InputOption::VALUE_REQUIRED, 'Shop name', 'Demo shop')
            ->addOption('shop-email', null, InputOption::VALUE_REQUIRED, 'Shop email address', 'your.email@shop.com')
            ->addOption('shop-currency', null, InputOption::VALUE_REQUIRED, 'Shop currency', 'EUR')
        ;
    }

    private function addAdminOptions()
    {
        $this
            ->addOption('skip-admin-creation', null, InputOption::VALUE_NONE, 'If provided, no admin user will be created.')
            ->addOption('admin-username', null, InputOption::VALUE_REQUIRED, 'Administrator username', 'demo')
            ->addOption('admin-password', null, InputOption::VALUE_REQUIRED, 'Administrator password', 'demo')
            ->addOption('admin-email', null, InputOption::VALUE_REQUIRED, 'Administrator email address', 'demo@demo.demo')
            ->addOption('admin-locale', null, InputOption::VALUE_REQUIRED, 'Administrator locale', 'de_DE')
            ->addOption('admin-name', null, InputOption::VALUE_REQUIRED, 'Administrator name', 'Demo user')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new InstallationRequest([
            'release' => $input->getOption('release'),
            'installDir' => $input->getOption('install-dir'),
            'dbHost' => $input->getOption('db-host'),
            'dbPort' => $input->getOption('db-port'),
            'dbSocket' => $input->getOption('db-socket'),
            'dbUser' => $input->getOption('db-user'),
            'dbPassword' => $input->getOption('db-password'),
            'dbName' => $input->getOption('db-name'),
            'shopLocale' => $input->getOption('shop-locale'),
            'shopHost' => $input->getOption('shop-host'),
            'shopPath' => $input->getOption('shop-path'),
            'shopName' => $input->getOption('shop-name'),
            'shopEmail' => $input->getOption('shop-email'),
            'shopCurrency' => $input->getOption('shop-currency'),
            'adminUsername' => $input->getOption('admin-username'),
            'adminPassword' => $input->getOption('admin-password'),
            'adminEmail' => $input->getOption('admin-email'),
            'adminLocale' => $input->getOption('admin-locale'),
            'adminName' => $input->getOption('admin-name'),
            'noSkipImport' => $input->getOption('no-skip-import'),
            'skipAdminCreation' => $input->getOption('skip-admin-creation')
        ]);

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

        $this->askBasePath($input, $ioService, $suggestion);

        $this->askDatabaseUser($input, $ioService);
        $this->askDatabasePassword($input, $ioService);
        $this->askDatabaseName($input, $ioService, $suggestion);
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
        $language = $input->getOption('shop-locale');
        if (!in_array($language, ['en_GB', 'de_DE'])) {
            throw new \RuntimeException("Invalid locale: '$language'");
        }
    }

    /**
     * Make sure, that some required fields are available
     *
     * @param InputInterface $input
     * @param IoService      $ioService
     */
    private function askGenericOptions(InputInterface $input, IoService $ioService)
    {
        $required = [
            'admin-username' => 'backend user name',
            'admin-password' => 'backend user password',
            'admin-name' => 'your full name',
            'admin-email' => 'your email'
        ];

        $config = $this->getConfig();

        foreach ($required as $field => $description) {
            // If field was set via cli option, use that
            if ($input->getOption($field)) {
                continue;
            }

            // else: check if the option is configured via config
            if (isset($config['ShopConfig']) && isset($config['ShopConfig'][$field])) {
                $input->setOption($field, $config['ShopConfig'][$field]);
                continue;
            }

            //
            $fieldData = $ioService->askAndValidate(
                "Please enter $description: ",
                [$this, 'genericValidator']
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
            $release = $ioService->ask('Please provide the release you want to install [latest]: ');
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
        $installDir = $input->getOption('install-dir');
        if (!$installDir) {
            $installDir = $ioService->askAndValidate(
                "Please provide the install directory [{$suggestion}]: ",
                [$this, 'validateInstallDir']
            );
            $input->setOption('install-dir', trim($installDir) ? $installDir : $suggestion);

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
        $databaseName = $input->getOption('db-name');
        if (!$databaseName) {
            $databaseName = $ioService->ask("Please provide the database name you want to use [{$suggestion}]: ");
            $input->setOption('db-name', trim($databaseName) ? $databaseName : $suggestion);
        }
    }

    /**
     * @param InputInterface $input
     * @param IoService      $ioService
     * @param string         $suggestion
     */
    private function askBasePath(InputInterface $input, IoService $ioService, $suggestion)
    {
        $basePath = $input->getOption('shop-path');
        if (!$basePath) {
            $suggestion = '/' . ltrim($suggestion, '/');
            $basePath = $ioService->ask("Please provide the shop base path you want to use [{$suggestion}]: ");
            $input->setOption('shop-path', trim($basePath) ? $basePath : $suggestion);
        }
    }

    /**
     * @param InputInterface $input
     * @param IoService $ioService
     */
    private function askDatabaseUser(InputInterface $input, IoService $ioService)
    {
        $databaseUser = $input->getOption('db-user');
        if (!$databaseUser) {
            $databaseUser = $ioService->ask("Please provide the database user: ");
            $input->setOption('db-user', trim($databaseUser));
        }
    }

    /**
     * @param InputInterface $input
     * @param IoService $ioService
     */
    private function askDatabasePassword(InputInterface $input, IoService $ioService)
    {
        $databasePassword = $input->getOption('db-password');
        if (!$databasePassword) {
            $databasePassword = $ioService->ask("Please provide the database password: ");
            $input->setOption('db-password', trim($databasePassword));
        }
    }
}
