<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Current;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
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

        return [
            self::CURRENT_DIR . "Components/SearchBundleDBAL/Condition/Condition.tpl" => "Components/SearchBundleDBAL/Condition/{$configuration->name}Condition.php",
            self::CURRENT_DIR . "Components/SearchBundleDBAL/Condition/ConditionHandler.tpl" => "Components/SearchBundleDBAL/Condition/{$configuration->name}ConditionHandler.php",
            self::CURRENT_DIR . "Components/SearchBundleDBAL/Facet/Facet.tpl" => "Components/SearchBundleDBAL/Facet/{$configuration->name}Facet.php",
            self::CURRENT_DIR . "Components/SearchBundleDBAL/Facet/FacetHandler.tpl" => "Components/SearchBundleDBAL/Facet/{$configuration->name}FacetHandler.php",
            self::CURRENT_DIR . "Components/SearchBundleDBAL/Sorting/Sorting.tpl" => "Components/SearchBundleDBAL/Sorting/Sorting.php",
            self::CURRENT_DIR . "Components/SearchBundleDBAL/Sorting/SortingHandler.tpl" => "Components/SearchBundleDBAL/Sorting/SortingHandler.php",
            self::CURRENT_DIR . "Components/SearchBundle/CriteriaRequestHandler.tpl" => "Components/SearchBundle/{$configuration->name}CriteriaRequestHandler.php",
            self::CURRENT_DIR . "Subscriber/SearchBundle.tpl" => "Subscriber/SearchBundle.php",
            self::CURRENT_DIR . "Resources/views/frontend/listing/actions/action-sorting.tpl" => "Resources/views/frontend/listing/actions/action-sorting.tpl"
        ];
    }
}
