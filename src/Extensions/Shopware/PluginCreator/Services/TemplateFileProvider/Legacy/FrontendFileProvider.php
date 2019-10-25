<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Legacy;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class FrontendFileProvider returns files related to the frontend controller / view
 */
class FrontendFileProvider implements FileProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasFrontend) {
            return [];
        }

        return [
            self::LEGACY_DIR . 'Controllers/Frontend.tpl' => "Controllers/Frontend/{$configuration->name}.php",
            self::LEGACY_DIR . 'Views/frontend/plugin_name/index.tpl' => "Views/frontend/{$nameGenerator->under_score_js}/index.tpl",
        ];
    }
}
