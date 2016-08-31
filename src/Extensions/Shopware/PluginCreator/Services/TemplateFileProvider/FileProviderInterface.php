<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Interface FileProviderInterface defines a FileProvider that is able to return a list
 * of files for the template engine to process. Every FileProvider covers one specific case,
 * so that it probably will check "Configuration", if it is required at all.
 *
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
interface FileProviderInterface
{
    const CURRENT_DIR = "current/";
    const LEGACY_DIR = "legacy/";

    /**
     * @param Configuration $configuration
     * @param NameGenerator $nameGenerator
     * @return array Return an array of files (key = source, value = target). Return empty
     * array for NOOP
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator);
}
