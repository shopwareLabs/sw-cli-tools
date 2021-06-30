<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Install\Struct;

use ShopwareCli\Struct;

class InstallationRequest extends Struct
{
    /** @var string */
    protected $release;

    /** @var string */
    protected $installDir;

    /** @var string */
    protected $onlyUnpack;

    /** @var string */
    protected $skipDownload;

    /** @var string */
    protected $dbHost;

    /** @var string */
    protected $dbPort;

    /** @var string */
    protected $dbSocket;

    /** @var string */
    protected $dbUser;

    /** @var string */
    protected $dbPassword;

    /** @var string */
    protected $dbName;

    /** @var string */
    protected $shopLocale;

    /** @var string */
    protected $shopHost;

    /** @var string */
    protected $shopPath;

    /** @var string */
    protected $shopName;

    /** @var string */
    protected $shopEmail;

    /** @var string */
    protected $shopCurrency;

    /** @var string */
    protected $adminUsername;

    /** @var string */
    protected $adminPassword;

    /** @var string */
    protected $adminEmail;

    /** @var string */
    protected $adminLocale;

    /** @var string */
    protected $adminName;

    /** @var string */
    protected $noSkipImport;

    /** @var string */
    protected $skipAdminCreation;

    public function all()
    {
        return \get_object_vars($this);
    }

    public function getRelease(): string
    {
        return $this->release;
    }

    public function getInstallDir(): string
    {
        return $this->installDir;
    }

    public function getOnlyUnpack(): string
    {
        return $this->onlyUnpack;
    }

    public function getSkipDownload(): string
    {
        return $this->skipDownload;
    }

    public function getAbsoluteInstallDir(): string
    {
        return \realpath($this->installDir);
    }

    public function getDbHost(): string
    {
        return $this->dbHost;
    }

    public function getDbPort(): string
    {
        return $this->dbPort;
    }

    public function getDbSocket(): string
    {
        return $this->dbSocket;
    }

    public function getDbUser(): string
    {
        return $this->dbUser;
    }

    public function getDbPassword(): string
    {
        return $this->dbPassword;
    }

    public function getDbName(): string
    {
        return $this->dbName;
    }

    public function getShopLocale(): string
    {
        return $this->shopLocale;
    }

    public function getShopHost(): string
    {
        return $this->shopHost;
    }

    public function getShopPath(): string
    {
        return $this->shopPath;
    }

    public function getShopName(): string
    {
        return $this->shopName;
    }

    public function getShopEmail(): string
    {
        return $this->shopEmail;
    }

    public function getShopCurrency(): string
    {
        return $this->shopCurrency;
    }

    public function getAdminUsername(): string
    {
        return $this->adminUsername;
    }

    public function getAdminPassword(): string
    {
        return $this->adminPassword;
    }

    public function getAdminEmail(): string
    {
        return $this->adminEmail;
    }

    public function getAdminLocale(): string
    {
        return $this->adminLocale;
    }

    public function getAdminName(): string
    {
        return $this->adminName;
    }

    public function getNoSkipImport(): string
    {
        return $this->noSkipImport;
    }

    public function getSkipAdminCreation(): string
    {
        return $this->skipAdminCreation;
    }
}
