<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class ModelFileProvider returns model related files
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class ModelFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasModels) {
            return [];
        }

        if ($configuration->isLegacyPlugin) {
            return array(
                self::LEGACY_DIR . "Models/Model.tpl" => "Models/{$configuration->name}/{$nameGenerator->camelCaseModel}.php",
                self::LEGACY_DIR . "Models/Repository.tpl" => "Models/{$configuration->name}/Repository.php"
            );
        }

        return array(
            self::CURRENT_DIR . "Models/Model.tpl" => "Models/{$nameGenerator->camelCaseModel}.php",
            self::CURRENT_DIR . "Models/Repository.tpl" => "Models/Repository.php"
        );
    }
}
