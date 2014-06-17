<?php
namespace ShopwareCli\Services;

/**
 * Class FileDownloader
 * @package ShopwareCli\Services
 */
class StreamFileDownloader
{
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
     */
    public function download($sourceUrl, $destination)
    {

    }
}
