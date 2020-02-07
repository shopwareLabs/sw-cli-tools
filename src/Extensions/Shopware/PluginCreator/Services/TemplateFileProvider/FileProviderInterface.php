<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Interface FileProviderInterface defines a FileProvider that is able to return a list
 * of files for the template engine to process. Every FileProvider covers one specific case,
 * so that it probably will check "Configuration", if it is required at all.
 */
interface FileProviderInterface
{
    /**
     * Directory which holds the file structure for the current plugin system.
     */
    public const CURRENT_DIR = 'current/';

    /**
     * Directory which holds the legacy file structure.
     */
    public const LEGACY_DIR = 'legacy/';

    /**
     * @return array Return an array of files (key = source, value = target). Return empty
     *               array for NOOP
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator);
}
