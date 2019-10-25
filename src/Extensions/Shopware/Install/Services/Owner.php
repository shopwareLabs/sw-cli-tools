<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Install\Services;

/**
 * The owner services changes the owning user and group of directories and files
 *
 * Class Owner
 */
class Owner
{
    /**
     * Set the user for a given directory or file
     *
     * @param $path
     * @param $user
     * @param bool $recursive
     */
    public function setUser($path, $user, $recursive)
    {
        chown($path, $user);

        if (!$recursive || !is_dir($path)) {
            return;
        }

        foreach ($this->getIterator($path) as $file) {
            chown($file, $user);
        }
    }

    /**
     * Set the group for a given directory or file
     *
     * @param $path
     * @param $group
     * @param bool $recursive
     */
    public function setGroup($path, $group, $recursive)
    {
        chgrp($path, $group);

        if (!$recursive || !is_dir($path)) {
            return;
        }

        foreach ($this->getIterator($path) as $file) {
            chgrp($file, $group);
        }
    }

    /**
     * Returns a flat iterator for a given directory
     *
     * @param $path
     *
     * @return \RecursiveIteratorIterator
     */
    private function getIterator($path)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);

        return $iterator;
    }
}
