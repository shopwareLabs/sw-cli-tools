<?php

namespace Plugin\ShopwareInstall\Services\Install;

use ShopwareCli\Application\Logger;
use ShopwareCli\Config;

use Plugin\ShopwareInstall\Services\Checkout;
use Plugin\ShopwareInstall\Services\VcsGenerator;
use Plugin\ShopwareInstall\Services\ConfigWriter;
use Plugin\ShopwareInstall\Services\Database;
use Plugin\ShopwareInstall\Services\Demodata;

/**
 * This install service will run all steps needed to setup shopware in the correct order
 *
 * Class Vcs
 * @package Plugin\ShopwareInstall\Services\Install
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

    public function __construct(
        Checkout $checkout,
        Config $config,
        VcsGenerator $vcsGenerator,
        ConfigWriter $configWriter,
        Database $database,
        Demodata $demodata

    )
    {
        $this->checkout = $checkout;
        $this->config = $config;
        $this->vcsGenerator = $vcsGenerator;
        $this->configWriter = $configWriter;
        $this->database = $database;
        $this->demoData = $demodata;
    }

    /**
     * Runs the steps needed to setup shopware
     *
     * @param $branch
     * @param $installDir
     * @param $basePath
     * @param $database
     * @param null $httpUser
     */
    public function installShopware($branch, $installDir, $basePath, $database, $httpUser = null)
    {
        $this->checkoutRepos($branch, $installDir, $httpUser);
        $this->generateVcsMapping($installDir);
        $this->writeShopwareConfig($installDir, $database);
        $this->writeBuildProperties($installDir, $basePath, $database);
        $this->setupDatabase($installDir,$database);
        $this->demoData->setup($installDir);

        Logger::info("<info>Install completed</info>");
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
        $core = $this->config['ShopwareInstallConfig']['Repos']['core'];
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
     * @param $installDir
     * @param $httpUser
     */
    private function checkoutRepos($branch, $installDir, $httpUser)
    {
        $core = $this->checkCoreConfig();

        $this->checkoutRepo($branch, $installDir, $httpUser, $core);

        foreach ($this->config['ShopwareInstallConfig']['Repos'] as $name => $repo) {
            if ($name == 'core') {
                continue;
            }
            $this->checkoutRepo('master', $installDir, $httpUser, $repo);
        }
    }

    /**
     * Create VCS mapping for phpstorm
     *
     * @param $installDir
     */
    private function generateVcsMapping($installDir)
    {
        $this->vcsGenerator->createVcsMapping($installDir, array_map(function ($repo) {
            return $repo['destination'];
        }, $this->config['ShopwareInstallConfig']['Repos']));
    }

    /**
     * Create config.php with the configured database credentials
     *
     * @param $installDir
     * @param $database
     */
    private function writeShopwareConfig($installDir, $database)
    {
        $this->configWriter->writeConfigPhp(
            $installDir,
            $this->config['ShopwareInstallConfig']['DatabaseConfig']['user'],
            $this->config['ShopwareInstallConfig']['DatabaseConfig']['pass'],
            $database,
            $this->config['ShopwareInstallConfig']['DatabaseConfig']['host']
        );
    }

    /**
     * Write the build properties file
     *
     * @param $installDir
     * @param $basePath
     * @param $database
     */
    private function writeBuildProperties($installDir, $basePath, $database)
    {
        $this->configWriter->writeBuildProperties(
            $installDir,
            $this->config['ShopwareInstallConfig']['ShopConfig']['host'],
            $basePath,
            $this->config['ShopwareInstallConfig']['DatabaseConfig']['user'],
            $this->config['ShopwareInstallConfig']['DatabaseConfig']['pass'],
            $database,
            $this->config['ShopwareInstallConfig']['DatabaseConfig']['host']
        );
    }

    /**
     * Run the database setup tool
     *
     * @param $installDir
     * @param $database
     */
    private function setupDatabase($installDir, $database)
    {
        $this->database->setup(
            $this->config['ShopwareInstallConfig']['DatabaseConfig']['user'],
            $this->config['ShopwareInstallConfig']['DatabaseConfig']['pass'],
            $database,
            $this->config['ShopwareInstallConfig']['DatabaseConfig']['host']
        );

        $this->database->runBuildScripts($installDir);

    }
}
