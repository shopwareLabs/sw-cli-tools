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

        return array(
            "Controllers/Frontend.tpl" => "Controllers/Frontend/{$configuration->name}.php",
            "Views/frontend/plugin_name/index.tpl" => "Views/frontend/{$nameGenerator->under_score_js}/index.tpl"
        );
    }
}
