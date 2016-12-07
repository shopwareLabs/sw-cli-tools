<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Current;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
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

        return [
            self::CURRENT_DIR . "Components/Api/Resource/Resource.tpl" => "Components/Api/Resource/{$nameGenerator->camelCaseModel}.php",
            self::CURRENT_DIR . "Controllers/Api.tpl" => "Controllers/Api/{$nameGenerator->camelCaseModel}.php",
            self::CURRENT_DIR . "Subscriber/ApiSubscriber.tpl" => "Subscriber/ApiSubscriber.php"
        ];
    }
}
