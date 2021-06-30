<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Resources;

use Shopware\DataGenerator\DataGenerator;
use Shopware\DataGenerator\RandomDataProvider;
use Shopware\DataGenerator\Struct\Config;
use Shopware\DataGenerator\Writer\WriterInterface;
use Shopware\DataGenerator\Writer\WriterManager;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class BaseResource
{
    /**
     * Stores the used ids for SQL inserts.
     *
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
     * @var DataGenerator
     */
    protected $generator;

    /**
     * @var IoService
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
     * @param string $field
     *
     * @return int|array
     */
    public function getIds($field = null)
    {
        if ($field) {
            return $this->ids[$field];
        }

        return $this->ids;
    }

    /**
     * Creates the data associated with the current resource and writes it to the
     * provided writer
     * May create additional writers using the existing WriterManager
     * All writers are automatically flushed once the data creation ends.
     */
    abstract public function create(WriterInterface $writer);

    /**
     * Initializes the main WriterInterface instance where the data will be written
     * Calls the create method
     * Flushes data at the end
     * Handles pre and pos query handling (truncating, enable/disable foreign keys).
     */
    final public function generateData(): void
    {
        $path = \explode('\\', \get_class($this));
        $writer = $this->writerManager->createWriter(\strtolower(\array_pop($path)), 'sql');
        $writer->write($this->prepareTables());

        $this->create($writer);
        $this->ioService->writeln('');

        $this->writerManager->flushAll();
        $writer->write($this->enableKeys());
        $this->writerManager->clear();
    }

    /**
     * Helper function which manages ids for a given type.
     *
     * @param string $type
     *
     * @return int
     */
    protected function getUniqueId($type)
    {
        if (empty($this->ids[$type])) {
            $this->ids[$type] = 1;

            return 1;
        }

        ++$this->ids[$type];

        return $this->ids[$type];
    }

    /**
     * Generic setup method which will truncate tables of a resource and disables keys for that table temporarily.
     */
    protected function prepareTables(): array
    {
        $sql = [
            'SET autocommit = 0;',
            'SET foreign_key_checks=0;',
            'SET unique_checks=0;',
            'SET @@session.sql_mode = "";',
        ];

        foreach ($this->tables as $table) {
            $sql[] = "TRUNCATE `{$table}`;";
            $sql[] = "ALTER TABLE `{$table}` DISABLE KEYS;";
        }
        $sql[] = 'COMMIT;';

        return $sql;
    }

    /**
     * Generic cleanup method which re-enables keys for the tables.
     *
     * @return string[]
     */
    protected function enableKeys(): array
    {
        $sql = [];

        foreach ($this->tables as $table) {
            $sql[] = "ALTER TABLE `{$table}` ENABLE KEYS;";
            $sql[] = 'SET unique_checks=1;';
        }
        $sql[] = 'COMMIT;';

        return $sql;
    }

    /**
     * @param int $number
     */
    protected function createProgressBar($number): void
    {
        $this->progressBar = $this->ioService->createProgressBar($number);
        $this->progressBar->start();
        $this->progressBar->setRedrawFrequency(1000);
    }

    /**
     * @param int $step
     */
    protected function advanceProgressBar($step = 1): void
    {
        if (!$this->progressBar) {
            return;
        }

        $this->progressBar->advance($step);
    }

    protected function finishProgressBar(): void
    {
        if (!$this->progressBar) {
            return;
        }

        $this->progressBar->finish();
    }
}
