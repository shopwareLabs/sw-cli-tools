<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\IoService;
use ShopwareCli\Utilities;

/**
 * Sets up the database by creating the database itself and running the build scripts to populate the database
 *
 * Class Database
 * @package Shopware\Install\Services
 */
class Database
{
    /** @var  Utilities */
    private $utilities;

    /** @var  \PDO */
    private $connection;
    /**
     * @var \ShopwareCli\Services\IoService
     */
    private $ioService;

    public function __construct(Utilities $utilities, IoService $ioService)
    {
        $this->utilities = $utilities;
        $this->ioService = $ioService;
    }

    private function createConnection($host, $username, $password)
    {
        $this->connection = new \PDO("mysql:host={$host};charset=utf8", $username, $password);
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

        $this->createConnection($host, $user, $password)->query("CREATE DATABASE `{$name}`;");
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

        if (file_exists($installDir . '/composer.json')) {
            $this->ioService->writeln("<info>Running build-composer-install</info>");
            $this->utilities->executeCommand("ant -f {$buildXml} build-composer-install");
        }

        if (file_exists($buildXml)) {
            $this->ioService->writeln("<info>Running build-database</info>");
            $this->utilities->executeCommand("ant -f {$buildXml} build-database");

            $this->ioService->writeln("<info>Running build-snippets-deploy</info>");
            if (file_exists($installDir . '/engine/Shopware/Commands/SnippetsToSqlCommand.php')) {
                $this->utilities->executeCommand("ant -f {$buildXml} build-snippets-deploy");
            }
        } else {
            $this->ioService->writeln("<error>Could not find {$buildXml}</error>");
            $this->ioService->writeln("<error>If you checked out an SW version < 4.1.0, you can just import {$installDir}/_sql/demo/VERSION.sql</error>");
        }
    }

    /**
     * Salt password for SW backend user
     *
     * @param $password
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

        if (!$fetchLanguageId) {
            throw new \RuntimeException("Could not resolve language ".$language);
        }

         // Drop previous inserted admins
        $this->getConnection()->query("DELETE FROM s_core_auth");

        // Insert new admin
        $prepareStatement = $this->getConnection()->prepare(
<<<'EOF'
INSERT INTO s_core_auth (roleID,username,password,localeID,`name`,email,active,admin,salted,lockeduntil)
VALUES (
    1,?,?,?,?,?,1,1,1,'0000-00-00 00:00:00'
)
EOF
        );
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
     * @param $installDir
     * @throws \RuntimeException
     */
    private function importBaseDelta($installDir)
    {
        $this->ioService->writeln("<info>Importing main delta</info>");

        $path42 = "{$installDir}/install/assets/sql/sw4_clean.sql";
        $path43 = "{$installDir}/recovery/./install/data/sql/sw4_clean.sql";

        if (!file_exists($path42) && !file_exists($path43)) {
            throw new \RuntimeException("Could not find setup delta");
        }

        $path = file_exists($path42) ? $path42 : $path43;

        $deltas = explode(";\n", file_get_contents($path));
        foreach ($deltas as $delta) {
            $this->getConnection()->exec($delta);
        }
    }

    /**
     * @param $installDir
     * @throws \RuntimeException
     */
    private function importSnippetDeltas($installDir)
    {
        $this->ioService->writeln("<info>Importing snippet delta</info>");

        $path42 = "{$installDir}/install/assets/sql/snippets.sql";
        $path43 = "{$installDir}/recovery/./install/data/sql/snippets.sql";

        if (!file_exists($path42) && !file_exists($path43)) {
            $this->ioService->writeln("<error>Could not import snippet deltas. This is only ok for shopware versions < 4.2</error>");

            return;
        }

        $path = file_exists($path42) ? $path42 : $path43;

        $deltas = explode(";\n", file_get_contents($path));
        foreach ($deltas as $delta) {
            $this->getConnection()->exec($delta);
        }
    }
}
