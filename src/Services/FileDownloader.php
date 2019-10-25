<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services;

/**
 * Class FileDownloader
 */
interface FileDownloader
{
    /**
     * @param string $sourceUrl
     * @param string $destination
     */
    public function download($sourceUrl, $destination);
}
