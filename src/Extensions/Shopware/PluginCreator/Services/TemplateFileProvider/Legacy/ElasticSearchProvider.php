<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Legacy;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

class ElasticSearchProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasElasticSearch) {
            return [];
        }

        return [
            self::LEGACY_DIR . 'Components/ESIndexingBundle/DataIndexer.tpl' => 'Components/ESIndexingBundle/DataIndexer.php',
            self::LEGACY_DIR . 'Components/ESIndexingBundle/Mapping.tpl' => 'Components/ESIndexingBundle/Mapping.php',
            self::LEGACY_DIR . 'Components/ESIndexingBundle/Provider.tpl' => 'Components/ESIndexingBundle/Provider.php',
            self::LEGACY_DIR . 'Components/ESIndexingBundle/Settings.tpl' => 'Components/ESIndexingBundle/Settings.php',
            self::LEGACY_DIR . 'Components/ESIndexingBundle/Synchronizer.tpl' => 'Components/ESIndexingBundle/Synchronizer.php',
            self::LEGACY_DIR . 'Components/SearchBundleES/Search.tpl' => 'Components/SearchBundleES/Search.php'
        ];
    }
}
