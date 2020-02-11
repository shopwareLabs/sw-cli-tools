<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Install\Command;

use Shopware\Install\Services\Install\Release;
use Shopware\Install\Struct\InstallationRequest;
use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Config;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShopwareInstallReleaseCommand extends BaseCommand
{
    public function interact(InputInterface $input, OutputInterface $output)
    {
        $this->validateInput($input);

        /** @var IoService $ioService */
        $ioService = $this->container->get('io_service');

        $this->askGenericOptions($input, $ioService);

        $suggestion = '';
        if (!$input->getOption('skip-download')) {
            $release = $this->askRelease($input, $ioService);
            $suggestion = $release ?: 'latest';

            $installDir = $this->askInstallationDirectory($input, $ioService, $suggestion);
            $suggestion = $installDir ?: $suggestion;
        }

        if (!$input->getOption('unpack-only')) {
            $this->askBasePath($input, $ioService, $suggestion);

            $this->askDatabaseUser($input, $ioService);
            $this->askDatabasePassword($input, $ioService);
            $this->askDatabaseName($input, $ioService, $suggestion);
        }
    }

    /**
     * @param string $input
     *
     * @throws \RuntimeException
     */
    public function genericValidator($input): string
    {
        if (empty($input)) {
            throw new \RuntimeException('Field may not be empty');
        }

        return $input;
    }

    /**
     * @throws \RuntimeException
     */
    public function validateInstallDir(?string $path): ?string
    {
        if (is_dir($path)) {
            throw new \RuntimeException("Path '{$path}' is not empty");
        }

        return $path;
    }

    protected function getConfig(): Config
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

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new InstallationRequest([
            'release' => $input->getOption('release'),
            'installDir' => $input->getOption('install-dir'),
            'onlyUnpack' => $input->getOption('unpack-only'),
            'skipDownload' => $input->getOption('skip-download'),
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
            'skipAdminCreation' => $input->getOption('skip-admin-creation'),
        ]);

        /** @var Release $installService */
        $installService = $this->container->get('shopware_release_install_service');
        $installService->installShopware($request);
    }

    /**
     * @throws \RuntimeException
     */
    protected function validateInput(InputInterface $input): void
    {
        $language = $input->getOption('shop-locale');
        if (!\in_array($language, ['en_GB', 'de_DE'])) {
            throw new \RuntimeException("Invalid locale: '$language'");
        }
    }

    private function addInstallerOptions(): void
    {
        $this
            ->addOption('release', 'r', InputOption::VALUE_REQUIRED, 'Release version. Default: Latest')
            ->addOption('install-dir', 'i', InputOption::VALUE_REQUIRED, 'Install directory')
            ->addOption('unpack-only', null, InputOption::VALUE_NONE, 'Only unpack the downloaded release')
            ->addOption('skip-download', null, InputOption::VALUE_NONE, 'Skip release downloading');
    }

    private function addDbOptions(): void
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

    private function addShopOptions(): void
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

    private function addAdminOptions(): void
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
     * Make sure, that some required fields are available
     */
    private function askGenericOptions(InputInterface $input, IoService $ioService): void
    {
        $required = [
            'admin-username' => 'backend user name',
            'admin-password' => 'backend user password',
            'admin-name' => 'your full name',
            'admin-email' => 'your email',
        ];

        $config = $this->getConfig();

        foreach ($required as $field => $description) {
            // If field was set via cli option, use that
            if ($input->getOption($field)) {
                continue;
            }

            // else: check if the option is configured via config
            if (isset($config['ShopConfig'], $config['ShopConfig'][$field])) {
                $input->setOption($field, $config['ShopConfig'][$field]);
                continue;
            }

            $hidden = false;

            if ($field === 'admin-password') {
                $hidden = true;
            }

            $fieldData = $ioService->askAndValidate(
                "Please enter $description: ",
                [$this, 'genericValidator'],
                false,
                null,
                $hidden
            );
            $input->setOption($field, $fieldData);
        }
    }

    private function askRelease(InputInterface $input, IoService $ioService): string
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

    private function askInstallationDirectory(InputInterface $input, IoService $ioService, string $suggestion): ?string
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

    private function askDatabaseName(InputInterface $input, IoService $ioService, string $suggestion): void
    {
        $databaseName = $input->getOption('db-name');
        if (!$databaseName) {
            $databaseName = $ioService->ask("Please provide the database name you want to use [{$suggestion}]: ");
            $input->setOption('db-name', trim($databaseName) ? $databaseName : $suggestion);
        }
    }

    private function askBasePath(InputInterface $input, IoService $ioService, string $suggestion): void
    {
        $basePath = $input->getOption('shop-path');
        if (!$basePath) {
            $suggestion = '/' . ltrim($suggestion, '/');
            $basePath = $ioService->ask("Please provide the shop base path you want to use [{$suggestion}]: ");
            $input->setOption('shop-path', trim($basePath) ? $basePath : $suggestion);
        }
    }

    private function askDatabaseUser(InputInterface $input, IoService $ioService): void
    {
        $databaseUser = $input->getOption('db-user');
        if (!$databaseUser) {
            $databaseUser = $ioService->ask('Please provide the database user: ');
            $input->setOption('db-user', trim($databaseUser));
        }
    }

    private function askDatabasePassword(InputInterface $input, IoService $ioService): void
    {
        $databasePassword = $input->getOption('db-password');
        if (!$databasePassword) {
            $databasePassword = $ioService->ask('Please provide the database password: ', null, true);
            $input->setOption('db-password', trim($databasePassword));
        }
    }
}
