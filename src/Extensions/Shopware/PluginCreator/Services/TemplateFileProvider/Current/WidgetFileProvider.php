<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Current;

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
            self::CURRENT_DIR . "Resources/views/backend/widget/main.tpl" => "Resources/views/backend/{$nameGenerator->under_score_js}/widgets/{$nameGenerator->under_score_js}.js",
            self::CURRENT_DIR . "Subscriber/BackendWidget.tpl" => "Subscriber/BackendWidget.php",
            self::CURRENT_DIR . "Resources/snippets/backend/widget/labels.tpl" => "Resources/snippets/backend/widget/labels.ini",
            self::CURRENT_DIR . "Controllers/BackendWidget.tpl" => "Controllers/Backend/{$nameGenerator->backendWidgetController}.php"
        ];
    }
}
