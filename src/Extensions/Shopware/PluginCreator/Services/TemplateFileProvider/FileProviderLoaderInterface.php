<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

interface FileProviderLoaderInterface
{
    /**
     * Loads and returns all file providers.
     *
     * @return FileProviderInterface[]
     */
    public function load();
}
