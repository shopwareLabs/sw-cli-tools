<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\IoService;
use ShopwareCli\Services\PathProvider\PathProvider;
use ShopwareCli\Services\ShopwareInfo;
use ShopwareCli\Services\ProcessExecutor;

/**
 * Handles demo data and licenses
 *
 * Class Demodata
 * @package Shopware\Install\Services
 */
class Demodata
{
    /**
     * @var \ShopwareCli\Services\PathProvider\PathProvider
     */
    private $pathProvider;

    /**
     * @var string
     */
    private $demoUrl = 'http://releases.s3.shopware.com/test_images.zip';

    /**
     * @var \ShopwareCli\Services\IoService
     */
    private $ioService;
    /**
     * @var ShopwareInfo
     */
    private $shopwareInfo;

    /**
     * @var ProcessExecutor
     */
    private $processExecutor;

    /**
     * @param PathProvider $pathProvider
     * @param IoService $ioService
     * @param ShopwareInfo $shopwareInfo
     * @param ProcessExecutor $processExecutor
     */
    public function __construct(PathProvider $pathProvider, IoService $ioService, ShopwareInfo $shopwareInfo, ProcessExecutor $processExecutor)
    {
        $this->pathProvider = $pathProvider;
        $this->ioService = $ioService;
        $this->shopwareInfo = $shopwareInfo;
        $this->processExecutor = $processExecutor;
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

        $targetFile = md5($this->demoUrl) . '_demo.zip';

        if (!file_exists(($assetDir . '/' . $targetFile))) {
            $this->ioService->writeln("<info>Downloading demodata from shopware.de</info>");
            $this->processExecutor->execute("wget {$this->demoUrl} -O {$assetDir}/{$targetFile}");
            $this->ioService->writeln("<info>Unzipping demo data</info>");
            $this->processExecutor->execute("unzip -q {$assetDir}/{$targetFile} -d {$assetDir}");
        }

        // todo: This should be done in PHP
        $this->ioService->writeln("<info>Copying demo data to shop</info>");
        $this->processExecutor->execute("cp -rf {$assetDir}/files {$installDir}");
        $this->processExecutor->execute("cp -rf {$assetDir}/media {$installDir}");
        $this->processExecutor->execute("find " .$this->shopwareInfo->getCacheDir($installDir) ." -type d -exec chmod 777 {} \;", true);
        $this->processExecutor->execute("find " .$this->shopwareInfo->getMediaDir($installDir) ." -type d -exec chmod 777 {} \;", true);
        $this->processExecutor->execute("find " .$this->shopwareInfo->getFilesDir($installDir) ." -type d -exec chmod 777 {} \;", true);
        $this->processExecutor->execute("find " .$this->shopwareInfo->getCacheDir($installDir) ."  -type d -exec chmod 777 {} \;", true);
    }

    public function runLicenseImport($installDir)
    {
        if (file_exists("{$installDir}/bin/console")) {
            try {
                $this->runCliCommands($installDir);
            } catch (\RuntimeException $e) {
                $this->ioService->writeln("<comment>Skipping license import: {$e->getMessage()}</comment>");
            }
        }

        $this->ioService->writeln("<info>Clearing the cache</info>");

        $this->processExecutor->execute($this->shopwareInfo->getCacheDir($installDir) . "/clear_cache.sh");
    }

    /**
     * @param $installDir
     */
    private function runCliCommands($installDir)
    {
        $this->ioService->writeln("<info>Running license import</info>");

        $this->processExecutor->execute("{$installDir}/bin/console sw:generate:attributes");
        $this->processExecutor->execute("{$installDir}/bin/console sw:plugin:refresh");
        $this->processExecutor->execute("{$installDir}/bin/console sw:plugin:install SwagLicense --activate");

        $licenseFile = @getenv('HOME').'/licenses.txt';
        if (file_exists($licenseFile)) {
            $this->processExecutor->execute("{$installDir}/bin/console swaglicense:import {$licenseFile}");
        }
    }
}
