<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli;

class DirectoryFilterIterator extends \FilterIterator
{
    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        return $this->isValidDir($this->current());
    }

    private function isValidDir(\DirectoryIterator $fileInfo): bool
    {
        return $fileInfo->isDir()
            && !$fileInfo->isDot()
            // skip dot directories e.g. .git
            && \strpos($fileInfo->getBasename(), '.') !== 0;
    }
}
