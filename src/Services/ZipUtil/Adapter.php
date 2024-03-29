<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\ZipUtil;

abstract class Adapter implements \SeekableIterator, \Countable
{
    /**
     * @var int
     */
    protected $position;

    /**
     * @var int
     */
    protected $count;

    /**
     * @param int $position
     */
    public function seek($position)
    {
        $this->position = (int) $position;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->count > $this->position;
    }

    /**
     * @return array|bool
     */
    public function each()
    {
        if (!$this->valid()) {
            return false;
        }
        $result = [$this->key(), $this->current()];
        $this->next();

        return $result;
    }
}
