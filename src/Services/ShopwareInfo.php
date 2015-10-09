<?php

namespace ShopwareCli\Services;

/**
 * Provides general info about shopware installations.
 *
 * Will take care of version dependent things.
 *
 * Class ShopwareInfo
 * @package ShopwareCli\Services
 */
class ShopwareInfo
{
    public function getCacheDir($path)
    {
        $path = $this->normalizePath($path);

        if (file_exists($path . 'cache/clear_cache.sh')) {
            return $path . 'cache';
        }

        if (file_exists($path . 'var/cache/clear_cache.sh')) {
            return $path . 'var/cache';
        }

        throw new \RuntimeException('Cache path not found');
    }

    public function getLogDir($path)
    {
        $path = $this->normalizePath($path);

        if (file_exists($path . 'logs/.htaccess')) {
            return $path . 'logs';
        }

        if (file_exists($path . 'var/log/.htaccess')) {
            return $path . 'var/log';
        }

        throw new \RuntimeException('Log path not found');
    }

    public function getMediaDir($path)
    {
        return $this->normalizePath($path) . 'media';
    }

    public function getFilesDir($path)
    {
        return $this->normalizePath($path) . 'files';
    }

    /**
     * @param $path
     * @return string
     */
    private function normalizePath($path)
    {
        return rtrim($path, '/\\') . '/';
    }
}
