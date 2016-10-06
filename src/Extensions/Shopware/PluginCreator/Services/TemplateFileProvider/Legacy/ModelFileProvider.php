<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Legacy;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class ModelFileProvider returns model related files
 */
class ModelFileProvider implements FileProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasModels) {
            return [];
        }

        return [
            self::LEGACY_DIR.'Models/Model.tpl'      => "Models/{$configuration->name}/{$nameGenerator->camelCaseModel}.php",
            self::LEGACY_DIR.'Models/Repository.tpl' => "Models/{$configuration->name}/Repository.php"
        ];
    }
}
