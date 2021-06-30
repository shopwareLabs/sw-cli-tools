<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Install\Services\Install;

use Shopware\Install\Services\ConfigWriter;
use Shopware\Install\Services\Database;
use Shopware\Install\Services\Demodata;
use Shopware\Install\Services\PostInstall;
use Shopware\Install\Services\ReleaseDownloader;
use Shopware\Install\Services\VcsGenerator;
use Shopware\Install\Struct\InstallationRequest;
use ShopwareCli\Config;
use ShopwareCli\Services\IoService;
use ShopwareCli\Services\ProcessExecutor;

/**
 * This install service will run all steps needed to setup shopware in the correct order
 */
class Release
{
    /**
     * @var Config
     **/
    protected $config;

    /**
     * @var VcsGenerator
     */
    protected $vcsGenerator;

    /**
     * @var ConfigWriter
     */
    protected $configWriter;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var Demodata
     */
    protected $demoData;

    /**
     * @var ReleaseDownloader
     */
    private $releaseDownloader;

    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @var PostInstall
     */
    private $postInstall;

    /**
     * @var ProcessExecutor
     */
    private $processExecutor;

    public function __construct(
        ReleaseDownloader $releaseDownloader,
        Config $config,
        VcsGenerator $vcsGenerator,
        ConfigWriter $configWriter,
        Database $database,
        IoService $ioService,
        PostInstall $postInstall,
        ProcessExecutor $processExecutor
    ) {
        $this->releaseDownloader = $releaseDownloader;
        $this->config = $config;
        $this->vcsGenerator = $vcsGenerator;
        $this->configWriter = $configWriter;
        $this->database = $database;
        $this->ioService = $ioService;
        $this->postInstall = $postInstall;
        $this->processExecutor = $processExecutor;
    }

    public function installShopware(InstallationRequest $request): void
    {
        if (!$request->getSkipDownload()) {
            $this->releaseDownloader->downloadRelease($request->getRelease(), $request->getInstallDir());
        }

        if ($request->getOnlyUnpack()) {
            return;
        }

        if ($request->getRelease() === 'latest' || \version_compare($request->getRelease(), '5.1.2', '>=')) {
            $this->createDatabase($request);
            $this->createShopwareConfig($request);
            $this->runInstaller($request);
        } else {
            $this->generateVcsMapping($request->getAbsoluteInstallDir());
            $this->createShopwareConfig($request);
            $this->setupDatabase($request);
            $this->lockInstaller($request->getAbsoluteInstallDir());
        }

        $this->ioService->writeln('<info>Running post release scripts</info>');
        $this->postInstall->fixPermissions($request->getAbsoluteInstallDir());
        $this->postInstall->setupTheme($request->getAbsoluteInstallDir());
        $this->postInstall->importCustomDeltas($request->getDbName());
        $this->postInstall->runCustomScripts($request->getAbsoluteInstallDir());

        $this->ioService->writeln('<info>Install completed</info>');
    }

    private function createDatabase(InstallationRequest $request): void
    {
        $this->database->setup(
            $request->getDbUser() ?: $this->config['DatabaseConfig']['user'],
            $request->getDbPassword() ?: $this->config['DatabaseConfig']['pass'],
            $request->getDbName(),
            $request->getDbHost() ?: $this->config['DatabaseConfig']['host'],
            $request->getDbPort() ?: $this->config['DatabaseConfig']['port'] ?: 3306
        );
    }

    private function generateVcsMapping($installDir): void
    {
        $this->vcsGenerator->createVcsMapping($installDir, \array_column($this->config['ShopwareInstallRepos'], 'destination'));
    }

    private function runInstaller(InstallationRequest $request): void
    {
        $delegateOptions = [
            'dbHost', 'dbPort', 'dbSocket', 'dbUser', 'dbPassword', 'dbName',
            'shopLocale', 'shopHost', 'shopPath', 'shopName', 'shopEmail', 'shopCurrency',
            'adminUsername', 'adminPassword', 'adminEmail', 'adminLocale', 'adminName',
        ];

        $arguments = [];
        foreach ($request->all() as $key => $value) {
            if (!\in_array($key, $delegateOptions, true) || $value === '') {
                continue;
            }

            $key = \strtolower(\preg_replace('/[A-Z]/', '-$0', $key));
            $arguments[] = \sprintf('--%s="%s"', $key, $value);
        }

        if ($request->getNoSkipImport()) {
            $arguments[] = '--no-skip-import';
        }

        if ($request->getSkipAdminCreation()) {
            $arguments[] = '--skip-admin-creation';
        }

        $arguments = \implode(' ', $arguments);

        $this->processExecutor->execute("php {$request->getAbsoluteInstallDir()}/recovery/install/index.php {$arguments}");
    }

    /**
     * Write shopware's config.php
     */
    private function createShopwareConfig(InstallationRequest $request): void
    {
        $this->configWriter->writeConfigPhp(
            $request->getAbsoluteInstallDir(),
            $request->getDbUser() ?: $this->config['DatabaseConfig']['user'],
            $request->getDbPassword() ?: $this->config['DatabaseConfig']['pass'],
            $request->getDbName(),
            $request->getDbHost() ?: $this->config['DatabaseConfig']['host'],
            $request->getDbPort() ?: $this->config['DatabaseConfig']['port'] ?: 3306
        );
    }

    private function setupDatabase(InstallationRequest $request): void
    {
        $this->createDatabase($request);

        $this->database->importReleaseInstallDeltas($request->getAbsoluteInstallDir());

        if ($request->getSkipAdminCreation() !== true) {
            $this->database->createAdmin(
                $request->getAdminUsername(),
                $request->getAdminName(),
                $request->getAdminEmail(),
                $request->getAdminLocale(),
                $request->getAdminPassword()
            );
        }
    }

    /**
     * Create install.lock in SW5
     *
     * @param string $installDir
     */
    private function lockInstaller($installDir): void
    {
        if (\file_exists($installDir . '/recovery/install/data')) {
            \touch($installDir . '/recovery/install/data/install.lock');
        }
    }
}
