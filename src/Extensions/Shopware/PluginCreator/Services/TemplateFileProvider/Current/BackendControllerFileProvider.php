<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Current;

use Shopware\DataGenerator\Struct\Config;
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
            self::CURRENT_DIR.'Controllers/Backend.tpl' => "Controllers/Backend/{$configuration->name}.php"
        ];
    }
}
