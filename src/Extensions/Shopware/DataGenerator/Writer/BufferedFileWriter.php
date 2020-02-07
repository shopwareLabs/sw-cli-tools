<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Writer;

/**
 * Buffered file writer which will only write to disc every X writes
 */
class BufferedFileWriter implements WriterInterface
{
    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * @var int
     */
    protected $bufferCounter = 0;

    /**
     * @var
     */
    protected $fileName;

    /**
     * @var int
     */
    private $maxBufferSize;

    /**
     * @param string $file
     */
    public function __construct($file, $maxBufferSize = 50)
    {
        $this->fileName = $file;
        $this->fileHandle = fopen($file, 'wb');
        $this->maxBufferSize = $maxBufferSize;
    }

    public function setWriteBuffer($writeBuffer): void
    {
        $this->maxBufferSize = $writeBuffer;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * {@inheritdoc}
     */
    public function write($content)
    {
        if (!\is_array($content)) {
            $this->buffer[] = $content;
            ++$this->bufferCounter;
        } else {
            $this->buffer = array_merge($this->buffer, $content);
            $this->bufferCounter += \count($content);
        }
        if ($this->bufferCounter >= $this->maxBufferSize) {
            $this->flush();
        }
    }

    /**
     * Flush the buffer to disc
     */
    public function flush()
    {
        if (!$this->buffer) {
            return;
        }

        fwrite($this->fileHandle, implode("\n", $this->buffer) . "\n");
        $this->buffer = [];
        $this->bufferCounter = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 10;
    }
}
