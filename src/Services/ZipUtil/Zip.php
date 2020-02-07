<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\ZipUtil;

use ZipArchive;

class Zip extends Adapter
{
    /**
     * @var ZipArchive
     */
    protected $stream;

    /**
     * @param string $fileName
     * @param null   $flags
     *
     * @throws \Exception
     */
    public function __construct($fileName = null, $flags = null)
    {
        if (!\extension_loaded('zip')) {
            throw new \RuntimeException('The PHP extension "zip" is not loaded.');
        }

        $this->stream = new ZipArchive();

        if ($fileName !== null) {
            if (($retval = $this->stream->open($fileName, $flags)) !== true) {
                throw new \RuntimeException($this->getErrorMessage($retval, $fileName), $retval);
            }
            $this->position = 0;
            $this->count = $this->stream->numFiles;
        }
    }

    /**
     * @return Entry\Zip
     */
    public function current()
    {
        return new Entry\Zip($this->stream, $this->position);
    }

    /**
     * @param string $name
     *
     * @return resource
     */
    public function getStream($name)
    {
        return $this->stream->getStream($name);
    }

    /**
     * @param string $name
     *
     * @return false|string
     */
    public function getContents($name)
    {
        return $this->stream->getFromName($name);
    }

    /**
     * @return array|false
     */
    public function getEntry($position)
    {
        return $this->stream->statIndex($position);
    }

    public function close(): bool
    {
        return $this->stream->close();
    }

    /**
     * Give a meaningful error message to the user.
     *
     * @param int    $retval
     * @param string $file
     *
     * @return string
     */
    protected function getErrorMessage($retval, $file): ?string
    {
        switch ($retval) {
            case ZipArchive::ER_EXISTS:
                return sprintf("File '%s' already exists.", $file);
            case ZipArchive::ER_INCONS:
                return sprintf("Zip archive '%s' is inconsistent.", $file);
            case ZipArchive::ER_INVAL:
                return sprintf('Invalid argument (%s)', $file);
            case ZipArchive::ER_MEMORY:
                return sprintf('Malloc failure (%s)', $file);
            case ZipArchive::ER_NOENT:
                return sprintf("No such zip file: '%s'", $file);
            case ZipArchive::ER_NOZIP:
                return sprintf("'%s' is not a zip archive.", $file);
            case ZipArchive::ER_OPEN:
                return sprintf("Can't open zip file: %s", $file);
            case ZipArchive::ER_READ:
                return sprintf('Zip read error (%s)', $file);
            case ZipArchive::ER_SEEK:
                return sprintf('Zip seek error (%s)', $file);
            default:
                return sprintf("'%s' is not a valid zip archive, got error code: %s", $file, $retval);
        }
    }
}
