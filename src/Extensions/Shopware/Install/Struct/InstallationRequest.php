<?php

namespace Shopware\Install\Struct;

use ShopwareCli\Struct;

class InstallationRequest extends Struct
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $mail;

    /**
     * @var string
     */
    public $language;

    /**
     * @var string
     */
    public $release;

    /**
     * @var string
     */
    public $installDir;

    /**
     * @var string
     */
    public $basePath;

    /**
     * @var string
     */
    public $databaseName;
}
