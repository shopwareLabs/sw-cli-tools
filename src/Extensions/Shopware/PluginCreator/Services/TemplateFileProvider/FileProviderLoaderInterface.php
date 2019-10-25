<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

interface FileProviderLoaderInterface
{
    /**
     * Loads and returns all file providers.
     *
     * @return FileProviderInterface[]
     */
    public function load();
}
