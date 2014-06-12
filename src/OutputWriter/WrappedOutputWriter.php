<?php

namespace ShopwareCli\OutputWriter;

/**
 * Allows you to register custom functions as OutputWriterInterface.
 *
 * Class WrappedOutputWriter
 * @package ShopwareCli\OutputWriter
 */
class WrappedOutputWriter implements OutputWriterInterface
{
    protected $wrapped;

    public function __construct($wrapped)
    {
        $this->wrapped = $wrapped;
    }

    public function write($text)
    {
        call_user_func($this->wrapped, array($text));
    }

}
