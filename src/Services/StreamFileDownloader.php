<?php
namespace ShopwareCli\Services;

/**
 * Class FileDownloader
 * @package ShopwareCli\Services
 */
class StreamFileDownloader implements FileDownloader
{
    const BLOCKSIZE = 8192;
    
    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @param IoService $ioService
     */
    public function __construct(IoService $ioService)
    {
        $this->ioService = $ioService;
    }

    /**
     * @param string $sourceUrl
     * @param string $destination
     * @throws \RuntimeException
     */
    public function download($sourceUrl, $destination)
    {
        if (false === $readHandle = fopen($sourceUrl, "rb")) {
            throw new \RuntimeException(sprintf("Could not open URL '%s'.", $sourceUrl));
        }

        if (false === $writeHandle = fopen($destination, "wb")) {
            throw new \RuntimeException(sprintf("Could not write file: %s.", $destination));
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
     * @param resource $readHandle
     * @return int
     */
    private function getContentLengthFromStream($readHandle)
    {
        $info = stream_get_meta_data($readHandle);

        $size = 0;
        foreach ($info['wrapper_data'] as $field) {
            if (stripos($field, 'content-length') !== false) {
                list($header, $size) = explode(':', $field);
            }
        }

        return $size;
    }
}
