<?php

namespace ShopwareCli\OutputWriter;

/**
 * Simple output writer - will just write to STDOUT using echo
 *
 * Class StreamOutputWriter
 * @package ShopwareCli\OutputWriter
 */
class StreamOutputWriter implements OutputWriterInterface
{
    public function write($text)
    {
        echo $text . "\n";
    }
}
