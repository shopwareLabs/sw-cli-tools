<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Current;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class CommandFileProvider returns files required for the CLI command
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class CommandFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasCommands) {
            return [];
        }

        return [
            self::CURRENT_DIR . "Commands/Command.tpl" => "Commands/{$nameGenerator->camelCaseModel}.php"
        ];
    }
}
