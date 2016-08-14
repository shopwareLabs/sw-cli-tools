<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
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

        $defaultFiles = array(
            "PluginClass.tpl" => $pluginClassName,
            "plugin.xml.tpl" => "plugin.xml",
            "Readme.tpl" => "Readme.md",
            "LICENSE" => "LICENSE",
            "Subscriber/Frontend.tpl" => "Subscriber/Frontend.php",
            "phpunit.xml.dist.tpl" => "phpunit.xml.dist",
            "tests/PluginTest.tpl" => "tests/PluginTest.php"
        );

        if ($configuration->isLegacyPlugin) {
            unset($defaultFiles["PluginClass.tpl"]);
            unset($defaultFiles["plugin.xml.tpl"]);
            unset($defaultFiles["tests/PluginTest.tpl"]);

            $defaultFiles["Bootstrap.tpl"] = "Bootstrap.php";
            $defaultFiles["plugin.tpl"] = "plugin.json";
            $defaultFiles["tests/LegacyPluginTest.tpl"] = "tests/Test.php";
        }

        return $defaultFiles;
    }
}
