<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\IoService;
use ShopwareCli\Services\PathProvider\PathProvider;
use ShopwareCli\Utilities;

/**
 * Handles demodata and licenes
 *
 * Class Demodata
 * @package Shopware\Install\Services
 */
class Demodata
{
    /** @var  Utilities */
    private $utilities;

    /** @var \ShopwareCli\Services\PathProvider\PathProvider  */
    private $pathProvider;

    private $demoUrl = 'http://releases.s3.shopware.com/demo_4.2.0.zip';
    /**
     * @var \ShopwareCli\Services\IoService
     */
    private $ioService;

    public function __construct(Utilities $utilities, PathProvider $pathProvider, IoService $ioService)
    {
        $this->utilities = $utilities;
        $this->pathProvider = $pathProvider;
        $this->ioService = $ioService;
    }

    /**
     * @param string $installDir
     */
    public function setup($installDir)
    {
        $assetDir = $this->pathProvider->getAssetsPath();
        if (!is_dir($assetDir)) {
            mkdir($assetDir);
        }

        if (!file_exists(($assetDir . '/demo.zip'))) {
            $this->ioService->writeln("<info>Downloading demodata from shopware.de</info>");
            $this->utilities->executeCommand("wget {$this->demoUrl} -O {$assetDir}/demo.zip");
            $this->ioService->writeln("<info>Unzipping demo data</info>");
            $this->utilities->executeCommand("unzip -q {$assetDir}/demo.zip -d {$assetDir}");
        }

        // todo: This should be done in PHP
        $this->ioService->writeln("<info>Copying demo data to shop</info>");
        $this->utilities->executeCommand("cp -rf {$assetDir}/files {$installDir}");
        $this->utilities->executeCommand("cp -rf {$assetDir}/media {$installDir}");
        $this->utilities->executeCommand("find {$installDir}/cache -type d -exec chmod 777 {} \;", true);
        $this->utilities->executeCommand("find {$installDir}/media -type d -exec chmod 777 {} \;", true);
        $this->utilities->executeCommand("find {$installDir}/files -type d -exec chmod 777 {} \;", true);
        $this->utilities->executeCommand("find {$installDir}/logs  -type d -exec chmod 777 {} \;", true);
    }

    public function runLicenseImport($installDir)
    {

        if (file_exists("{$installDir}/bin/console")) {
            $this->runCliCommands($installDir);
        }

        $this->ioService->writeln("<info>Clearing the cache</info>");

        $this->utilities->executeCommand("{$installDir}/cache/clear_cache.sh");
    }

    /**
     * @param $installDir
     */
    public function runCliCommands($installDir)
    {
        $this->ioService->writeln("<info>Running license import</info>");

        $this->utilities->executeCommand("{$installDir}/bin/console sw:generate:attributes", true);
        $this->utilities->executeCommand("{$installDir}/bin/console sw:plugin:refresh", true);
        $this->utilities->executeCommand("{$installDir}/bin/console sw:plugin:install SwagLicense --activate", true);

        $licenseFile = @getenv('HOME') . '/licenses.txt';
        if (file_exists($licenseFile)) {
            $this->utilities->executeCommand("{$installDir}/bin/console swaglicense:import {$licenseFile}", true);
        }
    }
}
