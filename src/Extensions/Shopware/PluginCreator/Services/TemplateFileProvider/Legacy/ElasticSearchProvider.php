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
            self::LEGACY_DIR . 'Subscriber/ORMBacklogSubscriber.php' => 'Subscriber/ORMBacklogSubscriber.php',
            self::LEGACY_DIR . 'Components/ESIndexingBundle/Struct/Blog.tpl' => 'Components/ESIndexingBundle/Struct/Blog.php',
            self::LEGACY_DIR . 'Components/ESIndexingBundle/BlogDataIndexer.tpl' => 'Components/ESIndexingBundle/BlogDataIndexer.php',
            self::LEGACY_DIR . 'Components/ESIndexingBundle/BlogMapping.tpl' => 'Components/ESIndexingBundle/BlogMapping.php',
            self::LEGACY_DIR . 'Components/ESIndexingBundle/BlogProvider.tpl' => 'Components/ESIndexingBundle/BlogProvider.php',
            self::LEGACY_DIR . 'Components/ESIndexingBundle/BlogSettings.tpl' => 'Components/ESIndexingBundle/BlogSettings.php',
            self::LEGACY_DIR . 'Components/ESIndexingBundle/BlogSynchronizer.tpl' => 'Components/ESIndexingBundle/BlogSynchronizer.php',
            self::LEGACY_DIR . 'Components/SearchBundleES/BlogSearch.tpl' => 'Components/SearchBundleES/BlogSearch.php'
        ];
    }
}