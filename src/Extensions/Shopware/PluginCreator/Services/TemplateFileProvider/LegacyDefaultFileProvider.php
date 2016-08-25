<?php

namespace  Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class LegacyDefaultFileProvider returns the default files for legacy-plugins, always needed for a plugin
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class LegacyDefaultFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        return [
            "Readme.tpl" => "Readme.md",
            "LICENSE" => "LICENSE",
            "Subscriber/Frontend.tpl" => "Subscriber/Frontend.php",
            "phpunit.xml.dist.tpl" => "phpunit.xml.dist",
            "Bootstrap.tpl" => "Bootstrap.php",
            "plugin.tpl" => "plugin.json",
            "tests/LegacyPluginTest.tpl" => "tests/Test.php",
        ];
    }
}
