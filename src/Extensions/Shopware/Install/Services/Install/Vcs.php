<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Install\Services\Install;

use Shopware\Install\Services\Checkout;
use Shopware\Install\Services\ConfigWriter;
use Shopware\Install\Services\Database;
use Shopware\Install\Services\Demodata;
use Shopware\Install\Services\PostInstall;
use Shopware\Install\Services\VcsGenerator;
use ShopwareCli\Config;
use ShopwareCli\Services\IoService;

/**
 * This install service will run all steps needed to setup shopware in the correct order
 */
class Vcs
{
    /** @var Checkout */
    protected $checkout;

    /** @var Config */
    protected $config;

    /** @var VcsGenerator */
    protected $vcsGenerator;

    /** @var ConfigWriter */
    protected $configWriter;

    /** @var Database */
    protected $database;

    /** @var Demodata */
    protected $demoData;

    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @var PostInstall
     */
    private $postInstall;

    public function __construct(
        Checkout $checkout,
        Config $config,
        VcsGenerator $vcsGenerator,
        ConfigWriter $configWriter,
        Database $database,
        Demodata $demodata,
        IoService $ioService,
        PostInstall $postInstall
    ) {
        $this->checkout = $checkout;
        $this->config = $config;
        $this->vcsGenerator = $vcsGenerator;
        $this->configWriter = $configWriter;
        $this->database = $database;
        $this->demoData = $demodata;
        $this->ioService = $ioService;
        $this->postInstall = $postInstall;
    }

    /**
     * Runs the steps needed to setup shopware
     *
     * @param string $installDir
     * @param null   $httpUser
     * @param bool   $noDemoData
     */
    public function installShopware($branch, $installDir, $basePath, $database, $httpUser = null, $noDemoData = false): void
    {
        $this->checkoutRepos($branch, $installDir, $httpUser);

        // after the directory is created by git we can get the realpath
        $installDir = \realpath($installDir);

        $this->generateVcsMapping($installDir);
        $this->writeBuildProperties($installDir, $basePath, $database);
        $this->setupDatabase($installDir, $database);

        if (!$noDemoData) {
            $this->demoData->setup($installDir);
        }

        $this->ioService->writeln('<info>Running post release scripts</info>');
        $this->postInstall->fixPermissions($installDir);
        $this->postInstall->importCustomDeltas($database);
        $this->postInstall->runCustomScripts($installDir);
        $this->postInstall->fixShopHost($database);

        $this->ioService->writeln('<info>Install completed</info>');
    }

    private function getDestinationPath($installDir, $destination): string
    {
        return $installDir . $destination;
    }

    /**
     * Enforce a configured core repository
     *
     * @throws \RuntimeException
     *
     * @return $core
     */
    private function checkCoreConfig()
    {
        $core = $this->config['ShopwareInstallRepos']['core'];
        if (!$core['destination'] || !$core['ssh'] || !$core['http']) {
            throw new \RuntimeException('You need to have a repo "core" defined in the config.yaml of this plugin');
        }

        return $core;
    }

    /**
     * Checkout a given branch from a given repo
     */
    private function checkoutRepo($branch, $installDir, $httpUser, $repo): void
    {
        $type = $httpUser ? 'http' : 'ssh';

        $this->checkout->checkout($repo[$type], $branch, $this->getDestinationPath($installDir, $repo['destination']));
    }

    /**
     * Checkout all user defined repositories (except: core)
     *
     * @param string $installDir
     */
    private function checkoutRepos($branch, $installDir, $httpUser): void
    {
        $core = $this->checkCoreConfig();

        $this->checkoutRepo($branch, $installDir, $httpUser, $core);

        foreach ($this->config['ShopwareInstallRepos'] as $name => $repo) {
            if ($name == 'core') {
                continue;
            }
            $this->checkoutRepo('master', $installDir, $httpUser, $repo);
        }
    }

    /**
     * Create VCS mapping for phpstorm
     *
     * @param string $installDir
     */
    private function generateVcsMapping($installDir): void
    {
        $this->vcsGenerator->createVcsMapping($installDir, \array_column($this->config['ShopwareInstallRepos'], 'destination'));
    }

    /**
     * Write the build properties file
     *
     * @param string $installDir
     */
    private function writeBuildProperties($installDir, $basePath, $database): void
    {
        $this->configWriter->writeBuildProperties(
            $installDir,
            $this->config['ShopConfig']['host'],
            $basePath,
            $this->config['DatabaseConfig']['user'],
            $this->config['DatabaseConfig']['pass'],
            $database,
            $this->config['DatabaseConfig']['host'],
            isset($this->config['DatabaseConfig']['port']) ? $this->config['DatabaseConfig']['port'] : 3306
        );
    }

    /**
     * Run the database setup tool
     *
     * @param string $installDir
     */
    private function setupDatabase($installDir, $database): void
    {
        $this->database->setup(
            $this->config['DatabaseConfig']['user'],
            $this->config['DatabaseConfig']['pass'],
            $database,
            $this->config['DatabaseConfig']['host'],
            isset($this->config['DatabaseConfig']['port']) ? $this->config['DatabaseConfig']['port'] : 3306
        );

        $this->database->runBuildScripts($installDir);
    }
}
