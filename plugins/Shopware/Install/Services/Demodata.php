<?php

namespace Shopware\Install\Services;

use ShopwareCli\Application\Logger;
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

    private $pathProvider;

    private $demoUrl = 'http://releases.s3.shopware.com/demo_4.2.0.zip';

    public function __construct(Utilities $utilities, PathProvider $pathProvider)
    {
        $this->utilities = $utilities;
        $this->pathProvider = $pathProvider;
    }

    public function setup($installDir)
    {
        $assetDir = $this->pathProvider->getAssetsPath();
        if (!is_dir($assetDir)) {
            mkdir($assetDir);
        }

        if (!file_exists(($assetDir . '/demo.zip'))) {
            Logger::info("<info>Downloading demodata from shopware.de</info>");
            $this->utilities->executeCommand("wget {$this->demoUrl} -O {$assetDir}/demo.zip");
            Logger::info("<info>Unzipping demo data</info>");
            $this->utilities->executeCommand("unzip -q {$assetDir}/demo.zip -d {$assetDir}");
        }

        // todo: This should be done in PHP
        Logger::info("<info>Copying demo data to shop</info>");
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

        Logger::info("<info>Clearing the cache</info>");

        $this->utilities->executeCommand("{$installDir}/cache/clear_cache.sh");
    }

    /**
     * @param $installDir
     */
    public function runCliCommands($installDir)
    {
        Logger::info("<info>Running license import</info>");

        $this->utilities->executeCommand("{$installDir}/bin/console/ sw:generate:attributes");
        $this->utilities->executeCommand("{$installDir}/bin/console/ sw:plugin:refresh");
        $this->utilities->executeCommand("{$installDir}/bin/console/ sw:plugin:install SwagLicense --activate");

        $licenseFile = $this->pathProvider->getHomeDir() . '/licenses.txt';
        if (file_exists($licenseFile)) {
            $this->utilities->executeCommand("{$installDir}/bin/console/ swaglicense:import {$licenseFile}");
        }
    }
}
