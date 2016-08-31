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

        if ($configuration->isLegacyPlugin) {
            return $this->getLegacyFiles($configuration);
        }

        return array(
            self::CURRENT_DIR . "Components/SearchBundleDBAL/Condition/Condition.tpl" => "Components/SearchBundleDBAL/Condition/{$configuration->name}Condition.php",
            self::CURRENT_DIR . "Components/SearchBundleDBAL/Condition/ConditionHandler.tpl" => "Components/SearchBundleDBAL/Condition/{$configuration->name}ConditionHandler.php",
            self::CURRENT_DIR . "Components/SearchBundleDBAL/Facet/Facet.tpl" => "Components/SearchBundleDBAL/Facet/{$configuration->name}Facet.php",
            self::CURRENT_DIR . "Components/SearchBundleDBAL/Facet/FacetHandler.tpl" => "Components/SearchBundleDBAL/Facet/{$configuration->name}FacetHandler.php",
            self::CURRENT_DIR . "Components/SearchBundleDBAL/CriteriaRequestHandler.tpl" => "Components/SearchBundleDBAL/{$configuration->name}CriteriaRequestHandler.php",
            self::CURRENT_DIR . "Subscriber/SearchBundle.tpl" => "Subscriber/SearchBundle.php"
        );
    }

    /**
     * @param Configuration $configuration
     * @return array
     */
    private function getLegacyFiles(Configuration $configuration)
    {
        return array(
            self::LEGACY_DIR . "Components/SearchBundleDBAL/Condition/Condition.tpl" => "Components/SearchBundleDBAL/Condition/{$configuration->name}Condition.php",
            self::LEGACY_DIR . "Components/SearchBundleDBAL/Condition/ConditionHandler.tpl" => "Components/SearchBundleDBAL/Condition/{$configuration->name}ConditionHandler.php",
            self::LEGACY_DIR . "Components/SearchBundleDBAL/Facet/Facet.tpl" => "Components/SearchBundleDBAL/Facet/{$configuration->name}Facet.php",
            self::LEGACY_DIR . "Components/SearchBundleDBAL/Facet/FacetHandler.tpl" => "Components/SearchBundleDBAL/Facet/{$configuration->name}FacetHandler.php",
            self::LEGACY_DIR . "Components/SearchBundleDBAL/CriteriaRequestHandler.tpl" => "Components/SearchBundleDBAL/{$configuration->name}CriteriaRequestHandler.php",
            self::LEGACY_DIR . "Subscriber/SearchBundle.tpl" => "Subscriber/SearchBundle.php"
        );
    }
}
