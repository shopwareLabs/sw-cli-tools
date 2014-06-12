<?php

namespace Plugin\ShopwareInstall\Services;

use ShopwareCli\Application\Logger;
use ShopwareCli\Utilities;

class ReleaseDownloader
{
    const DOWNLOAD_URL_TEMPLATE = 'http://releases.s3.shopware.com/install_%s.zip';
    const BLOCKSIZE = 8192;
    const DOWNLOAD_URL_LATEST = 'http://install.s3.shopware.com/';
    const DOWNLOAD_UPDATE_API = 'http://update-api.shopware.com/v1/release/install';

    /**
     * @var \ShopwareCli\Utilities
     */
    private $utilities;
    /**
     * @var \ShopwareCli\Application\Logger
     */
    private $logger;
    private $cachePath;

    public function __construct(Utilities $utilities, Logger $logger, $cachePath)
    {
        $this->utilities = $utilities;
        $this->logger = $logger;
        $this->cachePath = $cachePath;
    }

    /**
     * Download a release and unzip it
     *
     * @param $release
     * @param $installDir
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

        $this->utilities->executeCommand("unzip {$zipLocation} -d {$installDir}");
    }

    /**
     * New releases can be downloaded via the update api and provide a sha1 hash
     *
     * @param $release
     * @return string
     * @throws \RuntimeException
     */
    private function downloadFromUpdateApi($release)
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
        $sha1_actual = sha1_file($target);
        if ($sha1 != $sha1_actual) {
            throw new \RuntimeException("Hash missmatch");
        }
        copy($target, $cacheFilePath);

        return $cacheFilePath;
    }

    /**
     * Older releases needs to be installed directly via the s3 url
     *
     * @param $release
     * @return string
     */
    private function downloadFromUrl($release)
    {
        $url = $this->getDownloadUrl($release);
        $target = $this->getTempFile();

        $cacheFile = $this->getCacheFilePath($release);

        if (!file_exists($cacheFile)) {
            $this->logger->info("<info>Downloading release {$release}</info>");
            $this->download($url, $target);
            copy($target, $cacheFile);
        } else {
            $this->logger->info("<info>Reading cached release download for {$release}</info>");
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

        $progressCounter = 0;
        $currentSize = 0;

        while (!feof($readHandle)) {
            $currentSize += fwrite($writeHandle, fread($readHandle, self::BLOCKSIZE));

            if ($currentSize >= $progressCounter * ($length / 100)) {
                $progressCounter++;

                $this->printProgress($progressCounter);
            }
        }

        $this->logger->info("\n Download finished");

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

    /**
     * @param $progressCounter  INT 1-100
     */
    private function printProgress($progressCounter)
    {
        $progress = str_pad("", $progressCounter - 1, "=") . '>';
        echo "\r[" . str_pad($progress, 100, " ") . "] {$progressCounter}%";
    }
}
