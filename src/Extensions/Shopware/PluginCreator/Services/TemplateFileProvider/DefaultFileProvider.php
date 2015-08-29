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
        return array(
            "Bootstrap.tpl" => "Bootstrap.php",
            "Readme.tpl" => "Readme.md",
            "LICENSE" => "LICENSE",
            "plugin.tpl" => "plugin.json",
            "Subscriber/Frontend.tpl" => "Subscriber/Frontend.php",
            "phpunit.xml.dist.tpl" => "phpunit.xml.dist",
            "tests/Test.tpl" => "tests/Test.php"
        );
    }

}