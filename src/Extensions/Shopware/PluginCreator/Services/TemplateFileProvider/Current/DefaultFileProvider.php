<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Current;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class DefaultFileProvider returns the default files, always needed for a plugin
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class DefaultFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        $pluginClassName = "{$configuration->name}.php";

        return [
            self::CURRENT_DIR . "PluginClass.tpl" => $pluginClassName,
            self::CURRENT_DIR . "plugin.xml.tpl" => "plugin.xml",
            self::CURRENT_DIR . "Readme.tpl" => "Readme.md",
            self::CURRENT_DIR . "LICENSE" => "LICENSE",
            self::CURRENT_DIR . "Subscriber/Frontend.tpl" => "Subscriber/Frontend.php",
            self::CURRENT_DIR . "phpunit.xml.dist.tpl" => "phpunit.xml.dist",
            self::CURRENT_DIR . "tests/PluginTest.tpl" => "tests/PluginTest.php",
            self::CURRENT_DIR . "Resources/services.xml.tpl" => "Resources/services.xml",
            self::CURRENT_DIR . "Resources/config.xml.tpl" => "Resources/config.xml",
            self::CURRENT_DIR . "Resources/menu.xml.tpl" => "Resources/menu.xml"
        ];
    }
}
