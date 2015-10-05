<?php

namespace ShopwareCli\Services;

/**
 * Class FileDownloader
 * @package ShopwareCli\Services
 */
interface FileDownloader
{
    /**
     * @param  string $sourceUrl
     * @param  string $destination
     * @return void
     */
    public function download($sourceUrl, $destination);
}
