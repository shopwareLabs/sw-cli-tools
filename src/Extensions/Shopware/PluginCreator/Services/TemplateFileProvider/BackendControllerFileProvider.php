<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

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
        if ($configuration->hasBackend || $configuration->hasWidget) {
            return array(
                "Controllers/Backend.tpl" => "Controllers/Backend/{$configuration->name}.php"
            );
        }

        return [];
    }

}