<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector;

interface RootDetectorInterface
{
    /**
     * @param string $path
     *
     * @return bool
     */
    public function isRoot($path);
}
