<?php

namespace Shopware\DataGenerator\Resources;

use Shopware\DataGenerator\RandomDataProvider;
use Shopware\DataGenerator\Struct\Config;
use Shopware\DataGenerator\Writer\WriterInterface;
use Shopware\DataGenerator\Writer\WriterManager;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class BaseResource
{
    /**
     * Stores the used ids for SQL inserts
     * @var array
     */
    protected $ids = [];

    /**
     * Tables a resource owns.
     */
    protected $tables = [];

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Plugin\ShopwarePluginCreator\DataGenerator
     */
    protected $generator;

    /**
     * @var \ShopwareCli\Services\IoService
     */
    protected $ioService;

    /**
     * @var WriterManager
     */
    protected $writerManager;

    /**
     * @var ProgressBar
     */
    protected $progressBar;

    /**
     * @param Config $config
     * @param RandomDataProvider $generator
     * @param IoService $ioService
     * @param WriterManager $writerManager
     */
    public function __construct(
        Config $config,
        RandomDataProvider $generator,
        IoService $ioService,
        WriterManager $writerManager
    ) {
        $this->config = $config;
        $this->generator = $generator;
        $this->ioService = $ioService;
        $this->writerManager = $writerManager;
    }

    /**
     * Helper function which manages ids for a given type
     * @param string $type
     * @return int
     */
    protected function getUniqueId($type)
    {
        if (empty($this->ids[$type])) {
            $this->ids[$type] = 1;

            return 1;
        }

        $this->ids[$type] += 1;

        return $this->ids[$type];
    }

    /**
     * @param string $field
     * @return integer
     */
    public function getIds($field = null)
    {
        if ($field) {
            return $this->ids[$field];
        }

        return $this->ids;
    }

    /**
     * Generic setup method which will truncate tables of a resource and disables keys for that table temporarily
     */
    protected function prepareTables()
    {
        $sql = [
            "SET foreign_key_checks=0;",
            "SET unique_checks=0;"
        ];

        foreach ($this->tables as $table) {
            $sql[] = "TRUNCATE `{$table}`;";
            $sql[] = "ALTER TABLE `{$table}` DISABLE KEYS;";
        }
        $sql[] = "COMMIT;";

        return $sql;
    }

    /**
     * Generic cleanup method which re-enables keys for the tables
     * @return string[]
     */
    protected function enableKeys()
    {
        $sql = [];

        foreach ($this->tables as $table) {
            $sql[] = "ALTER TABLE `{$table}` ENABLE KEYS;";
            $sql[] = "SET unique_checks=1;";
        }
        $sql[] = "COMMIT;";

        return $sql;
    }

    /**
     * @param integer $number
     */
    protected function createProgressBar($number)
    {
        $this->progressBar = $this->ioService->createProgressBar($number);
        $this->progressBar->start();
        $this->progressBar->setRedrawFrequency(1000);
    }

    /**
     * @param int $step
     */
    protected function advanceProgressBar($step = 1)
    {
        if (!$this->progressBar) {
            return;
        }

        $this->progressBar->advance($step);
    }

    protected function finishProgressBar()
    {
        if (!$this->progressBar) {
            return;
        }

        $this->progressBar->finish();
    }

    /**
     * Creates the data associated with the current resource and writes it to the
     * provided writer
     * May create additional writers using the existing WriterManager
     * All writers are automatically flushed once the data creation ends
     *
     * @param WriterInterface $writer
     * @return mixed
     */
    abstract public function create(WriterInterface $writer);

    /**
     * Initializes the main WriterInterface instance where the data will be written
     * Calls the create method
     * Flushes data at the end
     * Handles pre and pos query handling (truncating, enable/disable foreign keys)
     */
    final public function generateData()
    {
        $path = explode('\\', get_class($this));
        $writer = $this->writerManager->createWriter(strtolower(array_pop($path)), 'sql');
        $writer->write($this->prepareTables());

        $this->create($writer);
        $this->ioService->writeln("");

        $writer->write($this->enableKeys());
        $this->writerManager->flushAll();
        $this->writerManager->clear();
    }
}
