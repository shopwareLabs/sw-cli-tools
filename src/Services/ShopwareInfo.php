<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services;

/**
 * Provides general info about shopware installations.
 *
 * Will take care of version dependent things.
 */
class ShopwareInfo
{
    public function getCacheDir($path): string
    {
        $path = $this->normalizePath($path);

        if (\file_exists($path . 'cache/clear_cache.sh')) {
            return $path . 'cache';
        }

        if (\file_exists($path . 'var/cache/clear_cache.sh')) {
            return $path . 'var/cache';
        }

        throw new \RuntimeException('Cache path not found');
    }

    public function getLogDir($path): string
    {
        $path = $this->normalizePath($path);

        if (\file_exists($path . 'logs/.htaccess')) {
            return $path . 'logs';
        }

        if (\file_exists($path . 'var/log/.htaccess')) {
            return $path . 'var/log';
        }

        throw new \RuntimeException('Log path not found');
    }

    public function getMediaDir($path): string
    {
        return $this->normalizePath($path) . 'media';
    }

    public function getFilesDir($path): string
    {
        return $this->normalizePath($path) . 'files';
    }

    private function normalizePath($path): string
    {
        return \rtrim($path, '/\\') . '/';
    }
}
