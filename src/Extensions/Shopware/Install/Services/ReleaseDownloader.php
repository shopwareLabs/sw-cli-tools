<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Install\Services;

use GuzzleHttp\Client;
use ShopwareCli\Services\FileDownloader;
use ShopwareCli\Services\IoService;
use ShopwareCli\Services\OpenSSLVerifier;
use ShopwareCli\Services\ProcessExecutor;

class ReleaseDownloader
{
    private const DOWNLOAD_UPDATE_API = 'http://update-api.shopware.com/v1/releases/install';

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
     * @var OpenSSLVerifier
     */
    private $openSSLVerifier;

    /**
     * @param string $cachePath
     */
    public function __construct(
        ProcessExecutor $processExecutor,
        IoService $ioService,
        FileDownloader $downloader,
        OpenSSLVerifier $openSSLVerifier,
        $cachePath
    ) {
        $this->cachePath = $cachePath;
        $this->ioService = $ioService;
        $this->processExecutor = $processExecutor;
        $this->downloader = $downloader;
        $this->openSSLVerifier = $openSSLVerifier;
    }

    /**
     * Download a release and unzip it
     *
     * @param string $release
     * @param string $installDir
     */
    public function downloadRelease($release, $installDir): void
    {
        $this->ioService->writeln('<info>Downloading release</info>');
        $zipLocation = $this->downloadFromUpdateApi($release);

        if (!is_dir($installDir) && !mkdir($installDir) && !is_dir($installDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $installDir));
        }

        $this->ioService->writeln('<info>Unzipping archive</info>');
        $this->processExecutor->execute("unzip -qq {$zipLocation} -d {$installDir}");
    }

    /**
     * New releases can be downloaded via the update api and provide a sha1 hash
     *
     * @param string $release
     */
    private function downloadFromUpdateApi($release): string
    {
        $indexedReleases = $this->getIndexedReleasesList();

        if (\array_key_exists($release, $indexedReleases)) {
            $content = $indexedReleases[$release];
        } elseif ($release === 'latest') {
            $content = array_shift($indexedReleases);
        } else {
            throw new \RuntimeException(sprintf('Could not find release %s', $release));
        }

        $version = $content['version'];
        $url = $content['uri'];
        $sha1 = $content['sha1'];

        $target = $this->getTempFile();
        $cacheFilePath = $this->getCacheFilePath($version);

        if (file_exists($cacheFilePath)) {
            return $cacheFilePath;
        }

        $this->downloader->download($url, $target);
        $sha1Actual = sha1_file($target);
        if ($sha1 != $sha1Actual) {
            throw new \RuntimeException('Hash mismatch');
        }
        copy($target, $cacheFilePath);

        return $cacheFilePath;
    }

    /**
     * Loads a list of the latest releases from the update API
     * Returns them indexed by the Shopware version (e.g: 5.1.0)
     */
    private function getIndexedReleasesList(): array
    {
        $response = (new Client())->get(self::DOWNLOAD_UPDATE_API);
        $signature = $response->getHeader('X-Shopware-Signature');

        if ($this->openSSLVerifier->isSystemSupported()
            && !$this->openSSLVerifier->isValid($response->getBody(), $signature[0])
        ) {
            throw new \RuntimeException('API signature verification failed');
        }

        $releases = json_decode($response->getBody(), true);
        if (empty($releases)) {
            throw new \RuntimeException('Could not get releases list package');
        }

        $indexedReleases = [];
        foreach ($releases as $release) {
            $indexedReleases[$release['version']] = $release;
        }

        return $indexedReleases;
    }

    /**
     * Return a generic cache file name for a given release
     */
    private function getCacheFilePath($release): string
    {
        return $this->cachePath . "/{$release}.zip";
    }

    /**
     * Return a temp dir name
     */
    private function getTempFile(): string
    {
        return sys_get_temp_dir() . '/' . uniqid('release_download', true);
    }
}
