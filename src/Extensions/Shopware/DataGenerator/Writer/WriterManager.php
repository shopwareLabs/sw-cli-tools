<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Writer;

use Shopware\DataGenerator\Struct\Config;
use ShopwareCli\Services\IoService;

class WriterManager
{
    /**
     * @var WriterInterface[]
     */
    private $writers = [];

    /**
     * @var
     */
    private $defaultWriterType;

    /**
     * @var array
     */
    private $writerConfig = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var bool
     */
    private $displayImportMessage = false;

    /**
     * @var IoService
     */
    private $ioService;

    public function __construct(
        Config $config,
        IoService $ioService
    ) {
        $this->config = $config;
        $this->ioService = $ioService;
    }

    /**
     * @param string $resourceKey
     * @param string $type
     * @param null   $writerType
     */
    public function createWriter($resourceKey, $type = null, $writerType = null): WriterInterface
    {
        $writerType = $writerType ?: $this->defaultWriterType;

        switch ($writerType) {
            case 'database':
                if ($type === 'sql') {
                    $writer = new DatabaseWriter($this->writerConfig[$writerType]);
                    break;
                }

                // no break
            default:
                $resourcePath = getcwd() . '/output/' . $this->config->getOutputName() . $resourceKey;
                if ($type) {
                    $resourcePath .= '.' . $type;
                }
                if ($type === 'sql') {
                    $this->displayImportMessage = true;
                }
                $writer = new BufferedFileWriter($resourcePath);
        }

        $this->writers[] = $writer;

        return $writer;
    }

    /**
     * Flushes all known writers at once
     */
    public function flushAll(): void
    {
        $this->ioService->writeln('Flushing data...');

        $compare = static function (WriterInterface $a, WriterInterface $b) {
            if ($a->getPriority() === $b->getPriority()) {
                return 0;
            }

            return ($a->getPriority() > $b->getPriority()) ? -1 : 1;
        };

        $writers = $this->writers;
        usort($writers, $compare);

        foreach ($writers as $writer) {
            $writer->flush();
        }

        if ($this->displayImportMessage) {
            $this->ioService->writeln('Done. Please import SQLs using something like this:');
            $this->ioService->writeln('mysql --local-infile -uroot -proot DATABASE < FILE.sql');
        }
    }

    /**
     * Clears all writers
     */
    public function clear(): void
    {
        $this->writers = [];
    }

    public function setDefaultWriterType($defaultWriterType): void
    {
        $this->defaultWriterType = $defaultWriterType;
    }

    public function setConfig($writerType, $value): void
    {
        $this->writerConfig[$writerType] = $value;
    }
}
