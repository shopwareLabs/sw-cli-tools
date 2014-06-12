<?php

namespace Plugin\ShopwareInstall\Services;

use ShopwareCli\Application\Logger;
use ShopwareCli\Utilities;

/**
 * Sets up the database by creating the database itself and running the build scripts to populate the database
 *
 * Class Database
 * @package Plugin\ShopwareInstall\Services
 */
class Database
{
    /** @var  Utilities */
    private $utilities;

    /** @var  \PDO */
    private $connection;

    public function __construct(Utilities $utilities)
    {
        $this->utilities = $utilities;
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
        Logger::info("<info>Creating database $name</info>");

        $this->createConnection($host, $user, $password)->query("CREATE DATABASE `{$name}`;");
        $this->getConnection()->query("use `{$name}`;");
    }

    /**
     * Will install the deltas to setup a shop from a release file.
     *
     * todo: The way the sql deltas are splitted should be improved
     * todo: Support the "en" delta
     *
     * @param $installDir
     */
    public function importReleaseInstallDeltas($installDir)
    {
        $this->getConnection()->exec(<<<'EOF'
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

        Logger::info("<info>Importing main delta</info>");
        $lines = explode(";\n", file_get_contents("{$installDir}/install/assets/sql/sw4_clean.sql"));
        foreach ($lines as $line) {
            $this->getConnection()->exec($line);
        }

        $this->getConnection()->exec('
           SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
           SET time_zone = "+00:00";
           SET @locale_de_DE = (SELECT id FROM s_core_locales WHERE locale = "de_DE");
           SET @locale_en_GB = (SELECT id FROM s_core_locales WHERE locale = "en_GB");
       ');

        Logger::info("<info>Importing snippet delta</info>");
        $lines = explode(";\n", file_get_contents("{$installDir}/install/assets/sql/snippets.sql"));
        foreach ($lines as $line) {
            $this->getConnection()->exec($line);
        }
    }

    /**
     * @param $installDir
     */
    public function runBuildScripts($installDir)
    {
        $buildXml = $installDir . '/build/build.xml';

        if (file_exists($installDir . '/composer.json')) {
            Logger::info("<info>Running build-composer-install</info>");
            $this->utilities->executeCommand("ant -f {$buildXml} build-composer-install");
        }

        if (file_exists($buildXml)) {
            Logger::info("<info>Running build-database</info>");
            $this->utilities->executeCommand("ant -f {$buildXml} build-database");

            Logger::info("<info>Running build-snippets-deploy</info>");
            if (file_exists($installDir . '/engine/Shopware/Commands/SnippetsToSqlCommand.php')) {
                $this->utilities->executeCommand("ant -f {$buildXml} build-snippets-deploy");
            }
        } else {
            Logger::info("<error>Could not find {$buildXml}</error>");
            Logger::info("<error>If you checked out an SW version < 4.1.0, you can just import {$installDir}/_sql/demo/VERSION.sql</error>");
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
     * @param $user
     * @param $name
     * @param $mail
     * @param $language
     * @param $password
     * @return bool
     * @throws \RuntimeException
     */
    public function createAdmin($user, $name, $mail, $language, $password)
    {
        Logger::info("<info>Creating admin user $user</info>");

        $fetchLanguageId = $this->getConnection()->prepare("
        SELECT id FROM s_core_locales WHERE locale = ?
        ");
        $fetchLanguageId->execute(array($language));
        $fetchLanguageId = $fetchLanguageId->fetchColumn();

        if (!$fetchLanguageId) {
            throw new \RuntimeException("Could not resolve language ".$language);
        }

         // Drop previous inserted admins
        $this->getConnection()->query("
        DELETE FROM s_core_auth
        ");
        // Insert new admin

        $prepareStatement = $this->getConnection()->prepare("
        INSERT INTO s_core_auth (roleID,username,password,localeID,`name`,email,active,admin,salted,lockeduntil)
        VALUES (
        1,?,?,?,?,?,1,1,1,'0000-00-00 00:00:00'
        )
        ");
        $prepareStatement->execute(array(
            $user,
            $this->saltPassword($password),
            $fetchLanguageId,
            $name,
            $mail
        ));

        return true;

    }
}
