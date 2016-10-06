<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Legacy;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class DefaultFileProvider returns the default files, always needed for a plugin
 */
class DefaultFileProvider implements FileProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        return [
            self::LEGACY_DIR.'Readme.tpl'                 => 'Readme.md',
            self::LEGACY_DIR.'LICENSE'                    => 'LICENSE',
            self::LEGACY_DIR.'Subscriber/Frontend.tpl'    => 'Subscriber/Frontend.php',
            self::LEGACY_DIR.'phpunit.xml.dist.tpl'       => 'phpunit.xml.dist',
            self::LEGACY_DIR.'Bootstrap.tpl'              => 'Bootstrap.php',
            self::LEGACY_DIR.'plugin.tpl'                 => 'plugin.json',
            self::LEGACY_DIR.'tests/LegacyPluginTest.tpl' => 'tests/Test.php',
        ];
    }
}
