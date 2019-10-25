<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Cache;

interface CacheInterface
{
    /**
     * @param string $key
     * @param string $data
     * @param int    $valid
     *
     * @return bool
     */
    public function write($key, $data, $valid);

    /**
     * @param string $key
     *
     * @return string|false
     */
    public function read($key);

    public function delete($key);

    /**
     * @param $key
     *
     * @return bool
     */
    public function exists($key);

    public function clear();

    public function getKeys();
}
