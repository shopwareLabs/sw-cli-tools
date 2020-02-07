<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Install\Services;

use ShopwareCli\Services\IoService;

/**
 * The config writer is responsible for writing build.properties and config.php configuration files
 */
class ConfigWriter
{
    protected $configTemplate = <<<EOF
<?php
return array(
    'db' => array(
        'username' => '%s',
        'password' => '%s',
        'dbname' => '%s',
        'host' => '%s',
        'port' => '%s'
    )
);
EOF;

    protected $buildPropertiesTemplate = <<<EOF
app.host = %s
app.path = %s

db.name = %s
db.host = %s
db.user = %s
db.password = %s
db.port = %s

EOF;

    /**
     * @var IoService
     */
    private $ioService;

    public function __construct(IoService $ioService)
    {
        $this->ioService = $ioService;
    }

    public function writeConfigPhp($installDir, $user, $password, $name, $host, $port = 3306): void
    {
        $this->ioService->writeln('<info>Writing config.php</info>');

        $config = sprintf($this->configTemplate, $user, $password, $name, $host, $port);

        file_put_contents($installDir . '/config.php', $config);
    }

    public function writeBuildProperties($installDir, $shopHost, $shopPath, $dbUser, $dbPassword, $dbName, $dbHost, $port = 3306): void
    {
        $this->ioService->writeln('<info>Writing build.properties</info>');

        $shopPath = '/' . ltrim($shopPath, '/');

        $config = sprintf($this->buildPropertiesTemplate, $shopHost, $shopPath, $dbName, $dbHost, $dbUser, $dbPassword, $port);

        file_put_contents($installDir . '/build/build.properties', $config);
    }
}
