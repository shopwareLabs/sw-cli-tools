<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

class BackendControllerFileProvider implements FileProviderInterface
{
    public function getFileMapping(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if ($configuration->hasBackend || $configuration->hasWidget) {
            return array(
                "Controllers/Backend.tpl" => "Controllers/Backend/{$configuration->name}.php"
            );
        }

        return [];
    }

}