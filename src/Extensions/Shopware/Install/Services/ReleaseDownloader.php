<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\FileDownloader;
use ShopwareCli\Services\IoService;
use ShopwareCli\Services\ProcessExecutor;

class ReleaseDownloader
{
    const DOWNLOAD_URL_TEMPLATE = 'http://releases.s3.shopware.com/install_%s.zip';

    const DOWNLOAD_URL_LATEST = 'http://install.s3.shopware.com/';
    const DOWNLOAD_UPDATE_API = 'http://update-api.shopware.com/v1/releases/install';

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
     * @var FileDownloader
     */
    private $downloader;

    /**
     * @param ProcessExecutor $processExecutor
     * @param IoService $ioService
     * @param FileDownloader $downloader
     * @param string $cachePath
     */
    public function __construct(
        ProcessExecutor $processExecutor,
        IoService $ioService,
        FileDownloader $downloader,
        $cachePath
    ) {
        $this->cachePath = $cachePath;
        $this->ioService = $ioService;
        $this->processExecutor = $processExecutor;
        $this->downloader = $downloader;
    }

    /**
     * Download a release and unzip it
     *
     * @param string $release
     * @param string $installDir
     */
    public function downloadRelease($release, $installDir)
    {
        $zipLocation = $this->downloadFromUpdateApi($release);

        if (!is_dir($installDir)) {
            mkdir($installDir);
        }

        $this->processExecutor->execute("unzip {$zipLocation} -d {$installDir}");
    }

    /**
     * New releases can be downloaded via the update api and provide a sha1 hash
     *
     * @param $release
     * @return string
     */
    private function downloadFromUpdateApi($release)
    {
        $indexedReleases = $this->getIndexedReleasesList();

        if (array_key_exists($release, $indexedReleases)) {
            $content = $indexedReleases[$release];
        } else {
            if ($release == 'latest') {
                $content = array_shift($indexedReleases);
            } else {
                throw new \RuntimeException(sprintf("Could not find release %s", $release));
            }
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

        $this->downloader->download($url, $target);
        $sha1Actual = sha1_file($target);
        if ($sha1 != $sha1Actual) {
            throw new \RuntimeException("Hash mismatch");
        }
        copy($target, $cacheFilePath);

        return $cacheFilePath;
    }

    /**
     * Loads a list of the latest releases from the update API
     * Returns them indexed by the Shopware version (e.g: 5.1.0)
     *
     * @return array
     */
    private function getIndexedReleasesList()
    {
        $releases = json_decode(trim(file_get_contents(self::DOWNLOAD_UPDATE_API)), true);
        if (empty($releases)) {
            throw new \RuntimeException("Could not get releases list package");
        }

        $indexedReleases = [];
        foreach ($releases as $release) {
            $indexedReleases[$release['version']] = $release;
        }

        return $indexedReleases;
    }

    /**
     * Return a generic cache file name for a given release
     *
     * @param $release
     * @return string
     */
    private function getCacheFilePath($release)
    {
        return $this->cachePath."/{$release}.zip";
    }

    /**
     * Return a temp dir name
     *
     * @return string
     */
    private function getTempFile()
    {
        return sys_get_temp_dir().'/'.uniqid('release_download');
    }
}
