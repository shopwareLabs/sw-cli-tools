<?php

namespace Shopware\Install\Services;


use ShopwareCli\Config;
use ShopwareCli\Services\ProcessExecutor;

class PostInstall
{
    /**
     * @var \ShopwareCli\Services\ProcessExecutor
     */
    private $processExecutor;
    /**
     * @var Owner
     */
    private $owner;
    /**
     * @var \ShopwareCli\Config
     */
    private $config;

    public function __construct(ProcessExecutor $processExecutor, Owner $owner, Config $config)
    {
        $this->processExecutor = $processExecutor;
        $this->owner = $owner;
        $this->config = $config;
    }

    public function fixPermissions($directory)
    {
        $command = sprintf('chmod 0777 -R "%s"', $directory . '/logs');
        $this->processExecutor->execute($command);

        $command = sprintf('chmod 0777 -R "%s"', $directory . '/cache');
        $this->processExecutor->execute($command);

        $this->setUser($directory);
        $this->setGroup($directory);
    }

    /**
     * set the user for the shopware directory
     *
     * @param $directory
     */
    private function setUser($directory)
    {
        if (isset($this->config['ChangeOwner']['user'])) {
            $this->owner->setUser($directory, $this->config['ChangeOwner']['user'], true);
        }
    }

    /**
     * set the group for the shopware directory
     *
     * @param $directory
     */
    private function setGroup($directory)
    {
        if (isset($this->config['ChangeOwner']['group'])) {
            $this->owner->setGroup($directory, $this->config['ChangeOwner']['group'], true);
        }
    }
}