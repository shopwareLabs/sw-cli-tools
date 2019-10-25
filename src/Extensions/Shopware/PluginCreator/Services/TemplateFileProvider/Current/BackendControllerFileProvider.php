<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Current;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class BackendControllerFileProvider returns files for the backend controller
 */
class BackendControllerFileProvider implements FileProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasBackend && !$configuration->hasWidget) {
            return [];
        }

        return [
            self::CURRENT_DIR . 'Controllers/Backend.tpl' => "Controllers/Backend/{$configuration->name}.php",
        ];
    }
}
