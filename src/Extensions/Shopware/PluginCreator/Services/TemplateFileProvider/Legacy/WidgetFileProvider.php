<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Legacy;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class WidgetFileProvider returns file for the ExtJS backend widget
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class WidgetFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasWidget) {
            return [];
        }

        return [
            self::LEGACY_DIR . "Views/backend/widget/main.tpl" => "Views/backend/{$nameGenerator->under_score_js}/widgets/{$nameGenerator->under_score_js}.js",
            self::LEGACY_DIR . "Snippets/backend/widget/labels.tpl" => "Snippets/backend/widget/labels.ini"
        ];
    }
}
