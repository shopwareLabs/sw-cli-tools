<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class FilterFileProvider returns files needed for the frontend filter
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class FilterFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasFilter) {
            return [];
        }

        return array(
            "Components/SearchBundleDBAL/Condition/Condition.tpl" => "Components/SearchBundleDBAL/Condition/{$configuration->name}Condition.php",
            "Components/SearchBundleDBAL/Condition/ConditionHandler.tpl" => "Components/SearchBundleDBAL/Condition/{$configuration->name}ConditionHandler.php",
            "Components/SearchBundleDBAL/Facet/Facet.tpl" => "Components/SearchBundleDBAL/Facet/{$configuration->name}Facet.php",
            "Components/SearchBundleDBAL/Facet/FacetHandler.tpl" => "Components/SearchBundleDBAL/Facet/{$configuration->name}FacetHandler.php",
            "Components/SearchBundleDBAL/CriteriaRequestHandler.tpl" => "Components/SearchBundleDBAL/{$configuration->name}CriteriaRequestHandler.php",
            "Subscriber/SearchBundle.tpl" => "Subscriber/SearchBundle.php"
        );
    }

}