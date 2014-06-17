<?php

namespace Shopware\Plugin\Struct;

use ShopwareCli\Struct;

/**
 * Shopware plugin struct
 *
 * Class Plugin
 * @package Shopware\Plugin\Struct
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
