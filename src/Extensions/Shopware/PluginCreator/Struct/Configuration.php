<?php

namespace Shopware\PluginCreator\Struct;

use ShopwareCli\Struct;

/**
 * The config struct stores all info needed to create the plugin
 *
 * Class Configuration
 * @package Shopware\PluginCreator\Struct
 */
class Configuration extends Struct
{
    public $phpFileHeader = "<?php\n";

    // the PluginConfig part from the config.yaml file
    public $pluginConfig;

    // Name of the plugin: SwagMyPlugin
    public $name;
    // Namespace of the plugin: frontend / core / backend
    public $namespace;

    // frontend controller needed?
    public $hasFrontend;
    // backend application needed?
    public $hasBackend;
    // widgets needed?
    public $hasWidget;
    // api needed?
    public $hasApi;
    // models needed?
    public $hasModels;
    // commands needed ?
    public $hasCommands;
    // dbal facet / condition needed?
    public $hasFilter;

    // model for the backend ($hasBackend)
    public $backendModel;
    // license header
    public $licenseHeader;
    public $licenseHeaderPlain;
}
