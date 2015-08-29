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

        return array(
            "Models/Model.tpl" => "Models/{$configuration->name}/{$nameGenerator->camelCaseModel}.php",
            "Models/Repository.tpl" => "Models/{$configuration->name}/Repository.php"
        );
    }

}