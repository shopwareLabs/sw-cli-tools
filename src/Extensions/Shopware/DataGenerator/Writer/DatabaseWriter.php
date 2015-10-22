<?php

namespace Shopware\DataGenerator\Writer;

use PDO;

/**
 * Class ShopwareWriter
 * @package Shopware\DataGenerator\Writer
 */
class DatabaseWriter implements WriterInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $data = [];

    public function __construct($config, $resourceName)
    {
        $this->config = $config;
        $this->resourceName = $resourceName;
    }

    /**
     * Returns an instance of a buffered writer
     *
     * @param $content
     * @return BufferedFileWriter
     */
    public function write($content)
    {
        $this->data[] = $content;
    }

    /**
     * Flushes all known writer at once
     */
    public function flush()
    {
        /** @var PDO $connection */
        $connection = $this->connectToDatabase($this->config);

        $connection->beginTransaction();

        foreach ($this->data as $query) {
            $connection->query($query);
        }

        $connection->commit();
    }

    /**
     * @param $config
     * @return PDO
     */
    private function connectToDatabase($config)
    {
        $connection = new PDO(
            'mysql:host='.$config['host'].';dbname='.$config['dbname'], $config['username'], $config['password'],
            array(
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
            )
        );

        return $connection;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return -10;
    }
}
