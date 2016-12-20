<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Legacy;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class ControllerPathFileProvider returns the ControllerPath subscriber if needed
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class ControllerPathFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasBackend
            && !$configuration->hasFrontend
            && !$configuration->hasWidget
            && !$configuration->hasApi
        ) {
            return [];
        }

        return [
            self::LEGACY_DIR . 'Subscriber/ControllerPath.tpl' => 'Subscriber/ControllerPath.php',
        ];
    }
}
