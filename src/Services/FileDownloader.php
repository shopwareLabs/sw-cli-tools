<?php

namespace ShopwareCli\Services;

/**
 * Class FileDownloader
 */
interface FileDownloader
{
    /**
     * @param  string $sourceUrl
     * @param  string $destination
     *
     * @return void
     */
    public function download($sourceUrl, $destination);
}
