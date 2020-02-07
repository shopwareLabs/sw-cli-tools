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
     */
    public function write($key, $data, $valid): bool;

    /**
     * @param string $key
     *
     * @return string|false
     */
    public function read($key);

    public function delete($key): void;

    public function exists($key): bool;

    public function clear(): void;

    public function getKeys(): array;
}
