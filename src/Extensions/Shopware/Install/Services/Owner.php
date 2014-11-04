<?php

namespace Shopware\Install\Services;

/**
 * The owner services changes the owning user and group of directories and files
 *
 * Class Owner
 * @package Shopware\Install\Services
 */
class Owner
{
    /**
     * Set the user for a given directory or file
     *
     * @param $path
     * @param $user
     * @param boolean $recursive
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
     * @param boolean $recursive
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
     * @return \RecursiveIteratorIterator
     */
    private function getIterator($path)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);

        return $iterator;
    }
}
