<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

class ControllerPathFileProvider implements FileProviderInterface
{
    public function getFileMapping(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if ($configuration->hasBackend
            || $configuration->hasFrontend
            || $configuration->hasWidget
            || $configuration->hasApi
        ) {
            return array(
                "Subscriber/ControllerPath.tpl" => "Subscriber/ControllerPath.php",
            );
        }

        return [];
    }

}