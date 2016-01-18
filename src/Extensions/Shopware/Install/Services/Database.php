<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\IoService;
use ShopwareCli\Services\ProcessExecutor;
use ShopwareCli\Utilities;

/**
 * Sets up the database by creating the database itself and running the build scripts to populate the database
 *
 * Class Database
 * @package Shopware\Install\Services
 */
class Database
{
    /**
     * @var  Utilities
     */
    private $utilities;

    /**
     * @var  \PDO
     */
    private $connection;

    /**
     * @var \ShopwareCli\Services\IoService
     */
    private $ioService;

    /**
     * @var ProcessExecutor
     */
    private $processExecutor;

    /**
     * @param Utilities $utilities
     * @param IoService $ioService
     * @param ProcessExecutor $processExecutor
     */
    public function __construct(Utilities $utilities, IoService $ioService, ProcessExecutor $processExecutor)
    {
        $this->utilities = $utilities;
        $this->ioService = $ioService;
        $this->processExecutor = $processExecutor;
    }

    private function createConnection($host, $username, $password, $port = 3306)
    {
        $this->connection = new \PDO("mysql:host={$host};charset=utf8;port={$port}", $username, $password);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this->connection;
    }

    private function getConnection()
    {
        if (!$this->connection) {
            throw new \RuntimeException("Connection was not created");
        }

        return $this->connection;
    }

    public function setup($user, $password, $name, $host, $port = 3306)
    {
        $this->ioService->writeln("<info>Creating database $name</info>");

        $this->createConnection($host, $user, $password, $port)->query("CREATE DATABASE IF NOT EXISTS `{$name}`;");
        $this->getConnection()->query("use `{$name}`;");
    }

    /**
     * Will install the deltas to setup a shop from a release file.
     *
     * todo: The way the sql deltas are splitted should be improved
     * todo: Support the "en" delta
     *
     * @param string $installDir
     */
    public function importReleaseInstallDeltas($installDir)
    {
        $this->getConnection()->exec(
<<<'EOF'
            SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
            SET time_zone = "+00:00";
            SET FOREIGN_KEY_CHECKS = 0;
            /*!40101 SET @saved_cs_client     = @@character_set_client */;
            /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
            /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
            /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
            /*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
            /*!40103 SET TIME_ZONE='+00:00' */;
            /*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
            /*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
            /*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
            /*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
EOF
        );

        $this->importBaseDelta($installDir);

        $this->getConnection()->exec(
<<<'EOF'
           SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
           SET time_zone = "+00:00";
           SET @locale_de_DE = (SELECT id FROM s_core_locales WHERE locale = "de_DE");
           SET @locale_en_GB = (SELECT id FROM s_core_locales WHERE locale = "en_GB");
EOF
        );

        $this->importSnippetDeltas($installDir);
    }

    /**
     * @param $installDir
     */
    public function runBuildScripts($installDir)
    {
        $buildXml = $installDir . '/build/build.xml';
        if (!file_exists($buildXml)) {
            $this->ioService->writeln("<error>Could not find {$buildXml}</error>");
            $this->ioService->writeln(
                "<error>If you checked out an SW version < 4.1.0, you can just import {$installDir}/_sql/demo/VERSION.sql</error>"
            );

            return;
        }

        $this->ioService->writeln("<info>Running build-unit</info>");
        $command = sprintf('ant -f %s build-unit', $buildXml);
        $this->processExecutor->execute($command);
    }

    /**
     * Salt password for SW backend user
     *
     * @param  string $password
     * @return string
     */
    private function saltPassword($password)
    {
        return md5("A9ASD:_AD!_=%a8nx0asssblPlasS$" . md5($password));
    }

    /**
     * Create backend user
     *
     * @param  string            $user
     * @param  string            $name
     * @param  string            $mail
     * @param  string            $language
     * @param  string            $password
     * @return bool
     * @throws \RuntimeException
     */
    public function createAdmin($user, $name, $mail, $language, $password)
    {
        $this->ioService->writeln("<info>Creating admin user $user</info>");

        $fetchLanguageId = $this->getConnection()->prepare("SELECT id FROM s_core_locales WHERE locale = ?");
        $fetchLanguageId->execute(array($language));
        $fetchLanguageId = $fetchLanguageId->fetchColumn();

        $authTableVersion = $this->getConnection()->prepare("SELECT COUNT(*) as count FROM s_schema_version WHERE version = 411");
        $authTableVersion->execute();
        $authTableVersion = $authTableVersion->fetchColumn();

        if (!$fetchLanguageId) {
            throw new \RuntimeException("Could not resolve language ".$language);
        }

         // Drop previous inserted admins
        $this->getConnection()->query("DELETE FROM s_core_auth");

        // Insert new admin
        if ($authTableVersion) {
            $query = <<<'EOF'
INSERT INTO s_core_auth (roleID,username,password,localeID,`name`,email,active,lockeduntil)
VALUES (
    1,?,?,?,?,?,1,'0000-00-00 00:00:00'
)
EOF;
        } else {
            $query = <<<'EOF'
INSERT INTO s_core_auth (roleID,username,password,localeID,`name`,email,active,admin,salted,lockeduntil)
VALUES (
    1,?,?,?,?,?,1,1,1,'0000-00-00 00:00:00'
)
EOF;
        }

        $prepareStatement = $this->getConnection()->prepare($query);
        $prepareStatement->execute(array(
            $user,
            $this->saltPassword($password),
            $fetchLanguageId,
            $name,
            $mail
        ));

        return true;
    }

    /**
     * @param  string            $installDir
     * @throws \RuntimeException
     */
    private function importBaseDelta($installDir)
    {
        $this->ioService->writeln("<info>Importing main delta</info>");

        $installDataDir = $this->getInstallDataFolder($installDir);

        $path42 = "{$installDataDir}/sw4_clean.sql";
        $pathLatest = "{$installDataDir}/install.sql";

        if (!$installDataDir || (!file_exists($path42) && !file_exists($pathLatest))) {
            throw new \RuntimeException("Could not find setup delta");
        }

        $path = file_exists($path42) ? $path42 : $pathLatest;

        $deltas = explode(";\n", file_get_contents($path));
        foreach ($deltas as $delta) {
            $this->getConnection()->exec($delta);
        }
    }

    /**
     * @param  string            $installDir
     * @throws \RuntimeException
     */
    private function importSnippetDeltas($installDir)
    {
        $this->ioService->writeln("<info>Importing snippet delta</info>");

        $installDataDir = $this->getInstallDataFolder($installDir);
        $snippetsFilePath = "{$installDataDir}/snippets.sql";

        if (!file_exists($installDataDir) || !file_exists($snippetsFilePath)) {
            $this->ioService->writeln("<error>Could not import snippet deltas. This is only ok for shopware versions < 4.2</error>");

            return;
        }

        $deltas = explode(";\n", file_get_contents($snippetsFilePath));
        foreach ($deltas as $delta) {
            $this->getConnection()->exec($delta);
        }
    }

    private function getInstallDataFolder($installDir)
    {
        $path42 = "{$installDir}/install/assets/sql";
        $pathLatest = "{$installDir}/recovery/install/data/sql";

        if (!file_exists($path42) && !file_exists($pathLatest)) {
            return;
        }

        return file_exists($path42) ? $path42 : $pathLatest;
    }
}
