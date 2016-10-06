<?php

namespace Shopware\Plugin\Struct;

use ShopwareCli\Struct;

/**
 * Shopware plugin struct
 *
 * Class Plugin
 */
class DisplayPlugin extends Struct
{
    public $index;
    public $repoType;
    public $name;
    public $module;
    public $repository;

    /**
     * @param  Plugin  $plugin
     * @param  int $index
     *
     * @return static
     */
    public static function createFromPluginAndIndex(Plugin $plugin, $index)
    {
        return new static([
            'index'      => $index,
            'name'       => $plugin->name,
            'module'     => $plugin->module,
            'repoType'   => $plugin->repoType,
            'repository' => $plugin->repository,
        ]);
    }
}
