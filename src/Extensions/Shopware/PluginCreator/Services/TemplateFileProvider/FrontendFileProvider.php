<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class FrontendFileProvider returns files related to the frontend controller / view
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class FrontendFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasFrontend) {
            return [];
        }

        if ($configuration->isLegacyPlugin) {
            return $this->getLegacyFiles($configuration, $nameGenerator);
        }

        return [
            self::CURRENT_DIR . "Controllers/Frontend.tpl" => "Controllers/Frontend/{$configuration->name}.php",
            self::CURRENT_DIR . "Resources/views/frontend/plugin_name/index.tpl" => "Resources/views/frontend/{$nameGenerator->under_score_js}/index.tpl"
        ];
    }

    /**
     * @param Configuration $configuration
     * @param NameGenerator $nameGenerator
     * @return array
     */
    private function getLegacyFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        return [
            self::LEGACY_DIR . "Controllers/Frontend.tpl" => "Controllers/Frontend/{$configuration->name}.php",
            self::LEGACY_DIR . "Views/frontend/plugin_name/index.tpl" => "Views/frontend/{$nameGenerator->under_score_js}/index.tpl"
        ];
    }
}
