<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;


/**
 * Class ApiFileProvider returns files required for the API
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class ApiFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
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