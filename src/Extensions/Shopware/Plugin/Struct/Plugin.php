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
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $cloneUrlHttp;

    /**
     * @var string
     */
    public $cloneUrlSsh;

    /**
     * @var string
     */
    public $module;

    /**
     * @var string
     */
    public $repository;

    /**
     * @var string
     */
    public $repoType;

    /**
     * @var bool
     */
    public $isShopware6 = false;
}
