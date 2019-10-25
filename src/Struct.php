<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli;

/**
 * Base struct with a simple constructor allowing to create a struct by array
 *
 * Class Struct
 */
abstract class Struct
{
    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * @param string $name
     *
     * @throws \OutOfRangeException
     */
    public function __get($name)
    {
        throw new \OutOfRangeException("Unknown property \${$name} in " . get_class($this) . '.');
    }

    /**
     * @param string $name
     * @param $value
     *
     * @throws \OutOfRangeException
     */
    public function __set($name, $value)
    {
        throw new \OutOfRangeException("Unknown property \${$name} in " . get_class($this) . '.');
    }

    /**
     * @param string $name
     *
     * @throws \OutOfRangeException
     */
    public function __unset($name)
    {
        throw new \OutOfRangeException("Unknown property \${$name} in " . get_class($this) . '.');
    }
}
