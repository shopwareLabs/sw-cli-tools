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
 *
 * Class Plugin
 */
class Plugin extends Struct
{
    public $name;

    public $cloneUrlHttp;

    public $cloneUrlSsh;

    public $module;

    public $repository;

    public $repoType;
}
