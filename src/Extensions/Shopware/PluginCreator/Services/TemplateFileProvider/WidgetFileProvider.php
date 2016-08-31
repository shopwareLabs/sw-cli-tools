<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
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

        if ($configuration->isLegacyPlugin) {
            return $this->getLegacyPlugin($nameGenerator);
        }

        return [
            self::CURRENT_DIR . "Resources/views/backend/widget/main.tpl" => "Resources/views/backend/{$nameGenerator->under_score_js}/widgets/{$nameGenerator->under_score_js}.js",
            self::CURRENT_DIR . "Resources/snippets/backend/widget/labels.tpl" => "Resources/snippets/backend/widget/labels.ini"
        ];
    }

    /**
     * @param NameGenerator $nameGenerator
     * @return array
     */
    private function getLegacyPlugin(NameGenerator $nameGenerator)
    {
        return [
            self::LEGACY_DIR . "Views/backend/widget/main.tpl" => "Views/backend/{$nameGenerator->under_score_js}/widgets/{$nameGenerator->under_score_js}.js",
            self::LEGACY_DIR . "Snippets/backend/widget/labels.tpl" => "Snippets/backend/widget/labels.ini"
        ];
    }
}
