<?php

namespace Shopware\DataGenerator\Writer;

/**
 * Buffered file writer which will only write to disc every X writes
 *
 * Class BufferedFileWriter
 * @package Shopware\DataGenerator\Writer
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
    protected $buffer = array();

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

    public function __construct($file, $maxBufferSize = 50)
    {
        $this->fileName = $file;
        $this->fileHandle = fopen($file, 'w');
        $this->maxBufferSize = $maxBufferSize;
    }

    public function setWriteBuffer($writeBuffer)
    {
        $this->maxBufferSize = $writeBuffer;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Write a line to disc. If a buffer is configured, this might not happen immediately
     *
     * @param $content
     */
    public function write($content)
    {
        $this->buffer[] = $content;
        $this->bufferCounter += 1;
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

        fputs($this->fileHandle, implode("\n", $this->buffer) . "\n");
        $this->buffer = array();
        $this->bufferCounter = 0;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 10;
    }
}
