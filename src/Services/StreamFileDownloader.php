<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services;

class StreamFileDownloader implements FileDownloader
{
    private const BLOCKSIZE = 8192;

    /**
     * @var IoService
     */
    private $ioService;

    public function __construct(IoService $ioService)
    {
        $this->ioService = $ioService;
    }

    /**
     * @param string $sourceUrl
     * @param string $destination
     *
     * @throws \RuntimeException
     */
    public function download($sourceUrl, $destination)
    {
        $readHandle = fopen($sourceUrl, 'rb');
        if ($readHandle === false) {
            throw new \RuntimeException(sprintf("Could not open URL '%s'.", $sourceUrl));
        }

        $writeHandle = fopen($destination, 'wb');
        if ($writeHandle === false) {
            throw new \RuntimeException(sprintf('Could not write file: %s.', $destination));
        }

        $length = $this->getContentLengthFromStream($readHandle);

        $progress = $this->ioService->createProgressBar($length / 1024);
        $progress->start();

        // update every 0.5 megabytes
        $progress->setRedrawFrequency(524288 / 1024);

        $currentSize = 0;

        while (!feof($readHandle)) {
            $currentSize += fwrite($writeHandle, fread($readHandle, self::BLOCKSIZE));
            $progress->setProgress($currentSize / 1024);
        }
        $progress->finish();

        $this->ioService->writeln("\n Download finished");

        fclose($readHandle);
        fclose($writeHandle);
    }

    /**
     * @param resource $readHandle
     */
    private function getContentLengthFromStream($readHandle): int
    {
        $info = stream_get_meta_data($readHandle);

        $size = 0;
        foreach ($info['wrapper_data'] as $field) {
            if (stripos($field, 'content-length') !== false) {
                [, $size] = explode(':', $field);
            }
        }

        return $size;
    }
}
