<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\IoService;
use ShopwareCli\Services\PathProvider\PathProvider;
use ShopwareCli\Services\ProcessExecutor;
use ShopwareCli\Services\ShopwareInfo;

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
    private $demoUrl = 'http://releases.s3.shopware.com/test_images_since_5.1.zip';

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
        if (!is_dir($assetDir) && !mkdir($assetDir) && !is_dir($assetDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $assetDir));
        }

        $targetFile = md5($this->demoUrl) . '_demo.zip';

        if (!file_exists($assetDir . '/' . $targetFile)) {
            $this->ioService->writeln('<info>Downloading demodata from shopware.de</info>');
            $this->processExecutor->execute("wget {$this->demoUrl} -O {$assetDir}/{$targetFile}");
            $this->ioService->writeln('<info>Unzipping demo data</info>');
            $this->processExecutor->execute("unzip -q {$assetDir}/{$targetFile} -d {$assetDir}");
        }

        // todo: This should be done in PHP
        $this->ioService->writeln('<info>Copying demo data to shop</info>');
        $this->processExecutor->execute("cp -rf {$assetDir}/files {$installDir}");
        $this->processExecutor->execute("cp -rf {$assetDir}/media {$installDir}");
        $this->processExecutor->execute('find ' .$this->shopwareInfo->getCacheDir($installDir) ." -type d -exec chmod 777 {} \;", true);
        $this->processExecutor->execute('find ' .$this->shopwareInfo->getMediaDir($installDir) ." -type d -exec chmod 777 {} \;", true);
        $this->processExecutor->execute('find ' .$this->shopwareInfo->getFilesDir($installDir) ." -type d -exec chmod 777 {} \;", true);
        $this->processExecutor->execute('find ' .$this->shopwareInfo->getCacheDir($installDir) ."  -type d -exec chmod 777 {} \;", true);
    }
}
