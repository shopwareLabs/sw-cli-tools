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

        if ($configuration->isLegacyPlugin) {
            return $this->getLegacyFiles($nameGenerator);
        }

        return array(
            self::CURRENT_DIR . "Components/Api/Resource/Resource.tpl" => "Components/Api/Resource/{$nameGenerator->camelCaseModel}.php",
            self::CURRENT_DIR . "Controllers/Api.tpl" => "Controllers/Api/{$nameGenerator->camelCaseModel}.php",
        );
    }

    /**
     * @param NameGenerator $nameGenerator
     * @return array
     */
    private function getLegacyFiles(NameGenerator $nameGenerator)
    {
        return array(
            self::LEGACY_DIR . "Components/Api/Resource/Resource.tpl" => "Components/Api/Resource/{$nameGenerator->camelCaseModel}.php",
            self::LEGACY_DIR . "Controllers/Api.tpl" => "Controllers/Api/{$nameGenerator->camelCaseModel}.php",
        );
    }
}
