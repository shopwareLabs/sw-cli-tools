<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector;

class ShopwareRootDetector implements RootDetectorInterface
{
    public static function getDirectories(): array
    {
        return [
            '/engine',
            '/var',
            '/bin',
            '/vendor',
            '/files',
        ];
    }

    public static function getFiles(): array
    {
        return [
            '/shopware.php',
        ];
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isRoot($path)
    {
        return $this->validateDirectories($path) && $this->validateFiles($path);
    }

    /**
     * @param string $path
     */
    private function validateDirectories($path): bool
    {
        foreach (self::getDirectories() as $directory) {
            if (!\is_dir($path . $directory)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $path
     */
    private function validateFiles($path): bool
    {
        foreach (self::getFiles() as $file) {
            if (!\file_exists($path . $file)) {
                return false;
            }
        }

        return true;
    }
}
