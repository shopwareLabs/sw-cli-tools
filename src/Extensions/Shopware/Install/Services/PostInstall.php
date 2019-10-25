<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Install\Services;

use ShopwareCli\Config;
use ShopwareCli\Services\ProcessExecutor;
use ShopwareCli\Services\ShopwareInfo;

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
    /**
     * @var ShopwareInfo
     */
    private $shopwareInfo;

    public function __construct(ProcessExecutor $processExecutor, Owner $owner, Config $config, ShopwareInfo $shopwareInfo)
    {
        $this->processExecutor = $processExecutor;
        $this->owner = $owner;
        $this->config = $config;
        $this->shopwareInfo = $shopwareInfo;
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
        $command = sprintf('chmod 0777 -R "%s"', $this->shopwareInfo->getLogDir($directory));
        $this->processExecutor->execute($command, null, true);

        $command = sprintf('chmod 0777 -R "%s"', $this->shopwareInfo->getCacheDir($directory));
        $this->processExecutor->execute($command, null, true);

        if (file_exists($directory . '/web')) {
            $command = sprintf('chmod 0777 -R "%s"', $directory . '/web');
            $this->processExecutor->execute($command, null, true);
        }

        $command = sprintf('chmod +x  "%s"', $directory . '/bin/console');
        $this->processExecutor->execute($command, null, true);

        $command = sprintf('chmod +x  "%s"', $this->shopwareInfo->getCacheDir($directory) . '/clear_cache.sh');
        $this->processExecutor->execute($command, null, true);

        $this->setUser($directory);
        $this->setGroup($directory);
    }

    /**
     * Set up default theme settings
     *
     * @param $directory
     */
    public function setupTheme($directory)
    {
        if (!file_exists($directory . '/themes')) {
            return;
        }

        $command = sprintf('php bin/console sw:generate:attributes');
        $this->processExecutor->execute($command, $directory, true);

        $command = sprintf('php bin/console sw:theme:initialize');
        $this->processExecutor->execute($command, $directory, true);
    }

    /**
     * Import custom deltas
     *
     * @param $database
     *
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
        $port = isset($this->config['DatabaseConfig']['port']) ? $this->config['DatabaseConfig']['port'] : 3306;

        $connection = new \PDO("mysql:host={$host};charset=utf8;port={$port}", $username, $password);
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $connection;
    }
}
