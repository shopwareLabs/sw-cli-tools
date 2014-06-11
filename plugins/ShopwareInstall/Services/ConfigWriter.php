<?php

namespace Plugin\ShopwareInstall\Services;
use ShopwareCli\Application\Logger;

/**
 * The config writer is responsible for writing build.properties and config.php configuration files
 *
 * Class ConfigWriter
 * @package Plugin\ShopwareInstall\Services
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

EOF;


    public function writeConfigPhp($installDir, $user, $password, $name, $host, $port=3306)
    {
        Logger::info("<info>Writing config.php</info>");

        $config = sprintf($this->configTemplate, $user, $password, $name, $host, $port);

        file_put_contents($installDir . '/config.php', $config);
    }

    public function writeBuildProperties($installDir, $shopHost, $shopPath, $dbUser, $dbPassword, $dbName, $dbHost)
    {
        Logger::info("<info>Writing build.properties</info>");

        $shopPath = '/' . ltrim($shopPath, '/');

        $config = sprintf($this->buildPropertiesTemplate, $shopHost, $shopPath, $dbName, $dbHost, $dbUser, $dbPassword);

        file_put_contents($installDir . '/build/build.properties', $config);
    }

}