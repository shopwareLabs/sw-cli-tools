<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Writer;

/**
 * Generic write interface to abstract file operations
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
     * @param string|array $content
     */
    public function write($content);

    /**
     * Priority in which the writer must be flushed. Higher priority writers are flushed first
     */
    public function getPriority(): int;
}
