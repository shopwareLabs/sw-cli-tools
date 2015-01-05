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

    public function fixShopHost($database)
    {
        $connection = $this->getConnection();
        $connection->query("USE `{$database}`");

        $connection->exec('UPDATE s_core_shops SET host = NULL WHERE host = ""');
    }

    /**
     * Set permissions for the shopware directory
     *
     * @param string $directory
     */
    public function fixPermissions($directory)
    {
        $command = sprintf('chmod 0777 -R "%s"', $directory . '/logs');
        $this->processExecutor->execute($command, null, true);

        $command = sprintf('chmod 0777 -R "%s"', $directory . '/cache');
        $this->processExecutor->execute($command, null, true);

        $command = sprintf('chmod +x  "%s"', $directory . '/bin/console');
        $this->processExecutor->execute($command, null, true);

        $command = sprintf('chmod +x  "%s"', $directory . '/cache/clear_cache.sh');
        $this->processExecutor->execute($command, null, true);

        $this->setUser($directory);
        $this->setGroup($directory);
    }

    /**
     * Import custom deltas
     *
     * @param $database
     * @throws \RuntimeException
     */
    public function importCustomDeltas($database)
    {
        if (!isset($this->config['CustomDeltas'])) {
            return;
        }

        $connection = $this->getConnection();
        $connection->query("USE `{$database}`");

        foreach ($this->config['CustomDeltas'] as $file) {
            if (!file_exists($file)) {
                throw new \RuntimeException("File '{$file}' not found");
            }
            $connection->exec(file_get_contents($file));
        }
    }

    /**
     * Run user scripts
     *
     * @param string $path
     */
    public function runCustomScripts($path)
    {
        if (!isset($this->config['CustomScripts'])) {
            return;
        }

        foreach ($this->config['CustomScripts'] as $script) {
            $this->processExecutor->execute($script, $path, true);
        }
    }

    /**
     * set the user for the shopware directory
     *
     * @param $directory
     */
    private function setUser($directory)
    {
        if (isset($this->config['ChangeOwner'], $this->config['ChangeOwner']['user'])) {
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
        if (isset($this->config['ChangeOwner'], $this->config['ChangeOwner']['group'])) {
            $this->owner->setGroup($directory, $this->config['ChangeOwner']['group'], true);
        }
    }

    /**
     * Get a PDO connection
     *
     * @return \PDO
     */
    private function getConnection()
    {
        $username = $this->config['DatabaseConfig']['user'];
        $password = $this->config['DatabaseConfig']['pass'];
        $host = $this->config['DatabaseConfig']['host'];

        $connection = new \PDO("mysql:host={$host};charset=utf8", $username, $password);
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $connection;
    }
}
