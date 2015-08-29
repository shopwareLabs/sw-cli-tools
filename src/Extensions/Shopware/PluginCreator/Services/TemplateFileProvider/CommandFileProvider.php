<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

class CommandFileProvider implements FileProviderInterface
{
    public function getFileMapping(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasCommands) {
            return [];
        }

        return array(
            "Commands/Command.tpl" => "Commands/{$nameGenerator->camelCaseModel}.php"
        );
    }

}