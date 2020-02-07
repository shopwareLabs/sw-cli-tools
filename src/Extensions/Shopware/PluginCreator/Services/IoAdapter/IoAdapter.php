<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\IoAdapter;

/**
 * IoAdapter is a generic interface for some file operations needed in the plugin
 */
interface IoAdapter
{
    /**
     * @return bool
     */
    public function exists($path);

    public function createDirectory($dir);

    public function createFile($file, $content);
}
