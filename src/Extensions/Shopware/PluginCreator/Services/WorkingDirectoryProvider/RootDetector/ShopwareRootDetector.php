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
    /**
     * @return array
     */
    public static function getDirectories()
    {
        return [
            '/engine',
            '/var',
            '/bin',
            '/vendor',
            '/files',
        ];
    }

    /**
     * @return array
     */
    public static function getFiles()
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
        if ($this->validateDirectories($path) && $this->validateFiles($path)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function validateDirectories($path)
    {
        foreach (self::getDirectories() as $directory) {
            if (!is_dir($path . $directory)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function validateFiles($path)
    {
        foreach (self::getFiles() as $file) {
            if (!file_exists($path . $file)) {
                return false;
            }
        }

        return true;
    }
}
