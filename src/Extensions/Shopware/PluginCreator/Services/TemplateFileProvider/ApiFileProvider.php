<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

class ApiFileProvider implements FileProviderInterface
{
    public function getFileMapping(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasApi) {
            return [];
        }

        return array(
            "Components/Api/Resource/Resource.tpl" => "Components/Api/Resource/{$nameGenerator->camelCaseModel}.php",
            "Controllers/Api.tpl" => "Controllers/Api/{$nameGenerator->camelCaseModel}.php",

        );
    }

}