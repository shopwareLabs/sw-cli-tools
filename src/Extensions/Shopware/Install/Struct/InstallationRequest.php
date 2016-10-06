<?php

namespace Shopware\Install\Struct;

use ShopwareCli\Struct;

class InstallationRequest extends Struct
{
    /** @var string */
    private $release;

    /** @var string */
    private $installDir;

    /** @var string */
    private $dbHost;

    /** @var string */
    private $dbPort;

    /** @var string */
    private $dbSocket;

    /** @var string */
    private $dbUser;

    /** @var string */
    private $dbPassword;

    /** @var string */
    private $dbName;

    /** @var string */
    private $shopLocale;

    /** @var string */
    private $shopHost;

    /** @var string */
    private $shopPath;

    /** @var string */
    private $shopName;

    /** @var string */
    private $shopEmail;

    /** @var string */
    private $shopCurrency;

    /** @var string */
    private $adminUsername;

    /** @var string */
    private $adminPassword;

    /** @var string */
    private $adminEmail;

    /** @var string */
    private $adminLocale;

    /** @var string */
    private $adminName;

    /** @var string */
    private $noSkipImport;

    /** @var string */
    private $skipAdminCreation;

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }
    }

    public function all()
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function getRelease()
    {
        return $this->release;
    }

    /**
     * @return string
     */
    public function getInstallDir()
    {
        return $this->installDir;
    }

    /**
     * @return string
     */
    public function getAbsoluteInstallDir()
    {
        return realpath($this->installDir);
    }

    /**
     * @return string
     */
    public function getDbHost()
    {
        return $this->dbHost;
    }

    /**
     * @return string
     */
    public function getDbPort()
    {
        return $this->dbPort;
    }

    /**
     * @return string
     */
    public function getDbSocket()
    {
        return $this->dbSocket;
    }

    /**
     * @return string
     */
    public function getDbUser()
    {
        return $this->dbUser;
    }

    /**
     * @return string
     */
    public function getDbPassword()
    {
        return $this->dbPassword;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @return string
     */
    public function getShopLocale()
    {
        return $this->shopLocale;
    }

    /**
     * @return string
     */
    public function getShopHost()
    {
        return $this->shopHost;
    }

    /**
     * @return string
     */
    public function getShopPath()
    {
        return $this->shopPath;
    }

    /**
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * @return string
     */
    public function getShopEmail()
    {
        return $this->shopEmail;
    }

    /**
     * @return string
     */
    public function getShopCurrency()
    {
        return $this->shopCurrency;
    }

    /**
     * @return string
     */
    public function getAdminUsername()
    {
        return $this->adminUsername;
    }

    /**
     * @return string
     */
    public function getAdminPassword()
    {
        return $this->adminPassword;
    }

    /**
     * @return string
     */
    public function getAdminEmail()
    {
        return $this->adminEmail;
    }

    /**
     * @return string
     */
    public function getAdminLocale()
    {
        return $this->adminLocale;
    }

    /**
     * @return string
     */
    public function getAdminName()
    {
        return $this->adminName;
    }

    /**
     * @return string
     */
    public function getNoSkipImport()
    {
        return $this->noSkipImport;
    }

    /**
     * @return string
     */
    public function getSkipAdminCreation()
    {
        return $this->skipAdminCreation;
    }
}
