<?php

namespace Shopware\DataGenerator\Writer;

/**
 * Generic write interface to abstract file operations
 *
 * Interface WriterInterface
 * @package Shopware\DataGenerator\Writer
 */
interface WriterInterface
{
    /**
     * Flushes the writer's content to its medium
     */
    public function flush();

    /**
     * Writes data into the writer. Depending on the actual implementation,
     * the data might not be actually written until flush() is called
     *
     * @param $content
     */
    public function write($content);

    /**
     * Priority in which the writer must be flushed. Higher priority writers are flushed first
     *
     * @return int
     */
    public function getPriority();
}
