<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

interface FileProviderInterface
{
    public function getFileMapping(Configuration $configuration, NameGenerator $nameGenerator);
}