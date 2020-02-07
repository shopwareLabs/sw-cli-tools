<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Struct;

use ShopwareCli\Struct;

/**
 * Shopware plugin struct
 */
class DisplayPlugin extends Struct
{
    public $index;

    public $repoType;

    public $name;

    public $module;

    public $repository;

    /**
     * @param int $index
     *
     * @return static
     */
    public static function createFromPluginAndIndex(Plugin $plugin, $index): DisplayPlugin
    {
        return new static([
            'index' => $index,
            'name' => $plugin->name,
            'module' => $plugin->module,
            'repoType' => $plugin->repoType,
            'repository' => $plugin->repository,
        ]);
    }
}
