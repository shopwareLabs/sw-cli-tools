<?php

namespace Shopware\Install\Services\Install;

use Shopware\Install\Services\PostInstall;
use Shopware\Install\Struct\InstallationRequest;
use ShopwareCli\Config;
use Shopware\Install\Services\ReleaseDownloader;
use Shopware\Install\Services\VcsGenerator;
use Shopware\Install\Services\ConfigWriter;
use Shopware\Install\Services\Database;
use Shopware\Install\Services\Demodata;
use ShopwareCli\Services\IoService;

/**
 * This install service will run all steps needed to setup shopware in the correct order
 *
 * Class Release
 * @package Shopware\Install\Services\Install
 */
class Release
{
    /**
     * @var Config
     **/
    protected $config;

    /**
     * @var  VcsGenerator
     */
    protected $vcsGenerator;

    /**
     * @var  ConfigWriter
     */
    protected $configWriter;

    /**
     * @var  Database
     */
    protected $database;

    /**
     * @var  Demodata
     */
    protected $demoData;

    /**
     * @var ReleaseDownloader
     */
    private $releaseDownloader;

    /**
     * @var \Shopware\Install\Services\Demodata
     */
    private $demodata;

    /**
     * @var \ShopwareCli\Services\IoService
     */
    private $ioService;
    /**
     * @var \Shopware\Install\Services\PostInstall
     */
    private $postInstall;

    /**
     * @param ReleaseDownloader $releaseDownloader
     * @param Config            $config
     * @param VcsGenerator      $vcsGenerator
     * @param ConfigWriter      $configWriter
     * @param Database          $database
     * @param Demodata          $demodata
     * @param IoService         $ioService
     */
    public function __construct(
        ReleaseDownloader $releaseDownloader,
        Config $config,
        VcsGenerator $vcsGenerator,
        ConfigWriter $configWriter,
        Database $database,
        Demodata $demodata,
        IoService $ioService,
        PostInstall $postInstall
    ) {
        $this->config = $config;
        $this->vcsGenerator = $vcsGenerator;
        $this->configWriter = $configWriter;
        $this->database = $database;
        $this->releaseDownloader = $releaseDownloader;
        $this->demodata = $demodata;
        $this->ioService = $ioService;
        $this->postInstall = $postInstall;
    }

    /**
     * @param InstallationRequest $request
     */
    public function installShopware(InstallationRequest $request)
    {
        $this->releaseDownloader->downloadRelease($request->release, $request->installDir);

        $this->generateVcsMapping($request->installDir);
        $this->writeShopwareConfig($request->installDir, $request->databaseName);
        $this->setupDatabase($request);

        $this->ioService->writeln("<info>Running post release scripts</info>");
        $this->postInstall->fixPermissions($request->installDir);
        $this->postInstall->importCustomDeltas($request->databaseName);
        $this->postInstall->runCustomScripts($request->installDir);

        $this->ioService->writeln("<info>Install completed</info>");
    }

    /**
     * Generate the VCS mapping for phpstorm
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
     * Write shopware's config.php
     *
     * @param string $installDir
     * @param string $database
     */
    private function writeShopwareConfig($installDir, $database)
    {
        $this->configWriter->writeConfigPhp(
            $installDir,
            $this->config['DatabaseConfig']['user'],
            $this->config['DatabaseConfig']['pass'],
            $database,
            $this->config['DatabaseConfig']['host']
        );
    }

    /**
     * Setup the database
     *
     * @param InstallationRequest $request
     */
    private function setupDatabase(InstallationRequest $request)
    {
        $this->database->setup(
            $this->config['DatabaseConfig']['user'],
            $this->config['DatabaseConfig']['pass'],
            $request->databaseName,
            $this->config['DatabaseConfig']['host']
        );
        $this->database->importReleaseInstallDeltas($request->installDir);
        $this->database->createAdmin(
            $request->username,
            $request->name,
            $request->name,
            $request->language,
            $request->password
        );
    }
}
