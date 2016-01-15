<?php

namespace Shopware\Install\Services\Install;

use Shopware\Install\Services\PostInstall;
use ShopwareCli\Config;
use Shopware\Install\Services\Checkout;
use Shopware\Install\Services\VcsGenerator;
use Shopware\Install\Services\ConfigWriter;
use Shopware\Install\Services\Database;
use Shopware\Install\Services\Demodata;
use ShopwareCli\Services\IoService;

/**
 * This install service will run all steps needed to setup shopware in the correct order
 *
 * Class Vcs
 * @package Shopware\Install\Services\Install
 */
class Vcs
{
    /** @var  Checkout */
    protected $checkout;

    /** @var Config */
    protected $config;

    /** @var  VcsGenerator */
    protected $vcsGenerator;

    /** @var  ConfigWriter */
    protected $configWriter;

    /** @var  Database */
    protected $database;

    /** @var  Demodata */
    protected $demoData;
    /**
     * @var \ShopwareCli\Services\IoService
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
     * @param $branch
     * @param string $installDir
     * @param $basePath
     * @param $database
     * @param null $httpUser
     * @param bool $noDemoData
     */
    public function installShopware($branch, $installDir, $basePath, $database, $httpUser = null, $noDemoData = false)
    {
        $this->checkoutRepos($branch, $installDir, $httpUser);

        // after the directory is created by git we can get the realpath
        $installDir = realpath($installDir);

        $this->generateVcsMapping($installDir);
        $this->writeBuildProperties($installDir, $basePath, $database);
        $this->setupDatabase($installDir, $database);
        $this->demoData->runLicenseImport($installDir);

        if (!$noDemoData) {
            $this->demoData->setup($installDir);
        }

        $this->ioService->writeln("<info>Running post release scripts</info>");
        $this->postInstall->fixPermissions($installDir);
        $this->postInstall->importCustomDeltas($database);
        $this->postInstall->runCustomScripts($installDir);
        $this->postInstall->fixShopHost($database);

        $this->ioService->writeln("<info>Install completed</info>");
    }

    private function getDestinationPath($installDir, $destination)
    {
        return $installDir . $destination;
    }

    /**
     * Enforce a configured core repository
     *
     * @return mixed
     * @throws \RuntimeException
     */
    private function checkCoreConfig()
    {
        $core = $this->config['ShopwareInstallRepos']['core'];
        if (!$core['destination'] || !$core['ssh'] || !$core['http']) {
            throw new  \RuntimeException('You need to have a repo "core" defined in the config.yaml of this plugin');
        }

        return $core;
    }

    /**
     * Checkout a given branch from a given repo
     *
     * @param $branch
     * @param $installDir
     * @param $httpUser
     * @param $repo
     */
    private function checkoutRepo($branch, $installDir, $httpUser, $repo)
    {
        $type = $httpUser ? 'http' : 'ssh';

        $this->checkout->checkout($repo[$type], $branch, $this->getDestinationPath($installDir, $repo['destination']));
    }

    /**
     * Checkout all user defined repositories (except: core)
     *
     * @param $branch
     * @param string $installDir
     * @param $httpUser
     */
    private function checkoutRepos($branch, $installDir, $httpUser)
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
    private function generateVcsMapping($installDir)
    {
        $this->vcsGenerator->createVcsMapping($installDir, array_map(function ($repo) {
            return $repo['destination'];
        }, $this->config['ShopwareInstallRepos']));
    }

    /**
     * Write the build properties file
     *
     * @param string $installDir
     * @param $basePath
     * @param $database
     */
    private function writeBuildProperties($installDir, $basePath, $database)
    {
        $this->configWriter->writeBuildProperties(
            $installDir,
            $this->config['ShopConfig']['host'],
            $basePath,
            $this->config['DatabaseConfig']['user'],
            $this->config['DatabaseConfig']['pass'],
            $database,
            $this->config['DatabaseConfig']['host']
        );
    }

    /**
     * Run the database setup tool
     *
     * @param string $installDir
     * @param $database
     */
    private function setupDatabase($installDir, $database)
    {
        $this->database->setup(
            $this->config['DatabaseConfig']['user'],
            $this->config['DatabaseConfig']['pass'],
            $database,
            $this->config['DatabaseConfig']['host']
        );

        $this->database->runBuildScripts($installDir);
    }
}
