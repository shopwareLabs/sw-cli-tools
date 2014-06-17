<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\IoService;
use ShopwareCli\Services\ProcessExecutor;

class ReleaseDownloader
{
    const DOWNLOAD_URL_TEMPLATE = 'http://releases.s3.shopware.com/install_%s.zip';
    const BLOCKSIZE = 8192;
    const DOWNLOAD_URL_LATEST = 'http://install.s3.shopware.com/';
    const DOWNLOAD_UPDATE_API = 'http://update-api.shopware.com/v1/release/install';

    /**
     * @var string
     */
    private $cachePath;
    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @var ProcessExecutor
     */
    private $processExecutor;

    /**
     * @param ProcessExecutor $processExecutor
     * @param IoService $ioService
     * @param string $cachePath
     */
    public function __construct(ProcessExecutor $processExecutor, IoService $ioService, $cachePath)
    {
        $this->cachePath = $cachePath;
        $this->ioService = $ioService;
        $this->processExecutor = $processExecutor;
    }

    /**
     * Download a release and unzip it
     *
     * @param string $release
     * @param string $installDir
     */
    public function downloadRelease($release, $installDir)
    {
        if ($release == 'latest') {
            $zipLocation = $this->downloadFromUpdateApi($release);
        } else {
            $zipLocation = $this->downloadFromUrl($release);
        }

        if (!is_dir($installDir)) {
            mkdir($installDir);
        }

        $this->processExecutor->execute("unzip {$zipLocation} -d {$installDir}");
    }

    /**
     * New releases can be downloaded via the update api and provide a sha1 hash
     *
     * @return string
     * @throws \RuntimeException
     */
    private function downloadFromUpdateApi()
    {
        $content = json_decode(trim(file_get_contents(self::DOWNLOAD_UPDATE_API)), true);
        if (empty($content)) {
            throw new \RuntimeException("Could not get latest install package");
        }

        $version = $content['version'];
        $url = $content['uri'];
        $size = $content['size'];
        $sha1 = $content['sha1'];

        $target = $this->getTempFile();
        $cacheFilePath = $this->getCacheFilePath($version);

        if (file_exists($cacheFilePath)) {
            return $cacheFilePath;
        }

        $this->download($url, $target);
        $sha1Actual = sha1_file($target);
        if ($sha1 != $sha1Actual) {
            throw new \RuntimeException("Hash missmatch");
        }
        copy($target, $cacheFilePath);

        return $cacheFilePath;
    }

    /**
     * Older releases needs to be installed directly via the s3 url
     *
     * @param string $release
     * @return string
     */
    private function downloadFromUrl($release)
    {
        $url = $this->getDownloadUrl($release);
        $target = $this->getTempFile();

        $cacheFile = $this->getCacheFilePath($release);

        if (!file_exists($cacheFile)) {
            $this->ioService->writeln("<info>Downloading release {$release}</info>");
            $this->download($url, $target);
            copy($target, $cacheFile);
        } else {
            $this->ioService->writeln("<info>Reading cached release download for {$release}</info>");
        }

        return $cacheFile;
    }

    /**
     * Download a file from $url to $file
     *
     * @param  string            $url
     * @param  string            $file
     * @throws \RuntimeException
     */
    private function download($url, $file)
    {
        if (false === $readHandle = fopen($url, "rb")) {
            throw new \RuntimeException(sprintf("Could not open URL '%s'.", $url));
        }

        if (false === $writeHandle = fopen($file, "wb")) {
            throw new \RuntimeException(sprintf("Could not write file: %s.", $file));
        }

        $length = $this->getContentLengthFromStream($readHandle);

        $progress = $this->ioService->createProgressBar($length/1024);
        $progress->start();

        // update every 0.5 magabytes
        $progress->setRedrawFrequency(524288/1024);

        $currentSize = 0;

        while (!feof($readHandle)) {
            $currentSize += fwrite($writeHandle, fread($readHandle, self::BLOCKSIZE));
            $progress->setCurrent($currentSize/1024);
        }
        $progress->finish();

        $this->ioService->writeln("\n Download finished");

        fclose($readHandle);
        fclose($writeHandle);
    }

    /**
     * Return a generic cache file name for a given release
     *
     * @param $release
     * @return string
     */
    private function getCacheFilePath($release)
    {
        return $this->cachePath . "/{$release}.zip";
    }

    /**
     * Return a temp dir name
     *
     * @return string
     */
    private function getTempFile()
    {
        return sys_get_temp_dir() . '/' . uniqid('release_download');
    }

    /**
     * get a release url for an older release
     *
     * @param $release
     * @return string
     */
    private function getDownloadUrl($release)
    {
        if ($release == 'latest') {
            return self::DOWNLOAD_URL_LATEST;
        }

        return sprintf(self::DOWNLOAD_URL_TEMPLATE, $release);
    }

    /**
     * @param $readHandle
     */
    private function getContentLengthFromStream($readHandle)
    {
        $info = stream_get_meta_data($readHandle);
        foreach ($info['wrapper_data'] as $field) {
            if (stripos($field, 'content-length') !== false) {
                list($header, $size) = explode(':', $field);
            }
        }

        return $size;
    }
}
