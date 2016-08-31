<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\DataGenerator\Struct\Config;
use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class BackendControllerFileProvider returns files for the backend controller
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class BackendControllerFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasBackend && !$configuration->hasWidget) {
            return [];
        }

        if ($configuration->isLegacyPlugin) {
            return $this->getLegacyFiles($configuration);
        }

        return [
            self::CURRENT_DIR . "Controllers/Backend.tpl" => "Controllers/Backend/{$configuration->name}.php"
        ];
    }

    /**
     * @param Configuration $configuration
     * @return array
     */
    private function getLegacyFiles(Configuration $configuration)
    {
        return [
            self::LEGACY_DIR . "Controllers/Backend.tpl" => "Controllers/Backend/{$configuration->name}.php"
        ];
    }
}
