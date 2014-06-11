<?php

namespace ShopwareCli\OutputWriter;

class StreamOutputWriter implements OutputWriterInterface
{
    public function write($text)
    {
        echo $text . "\n";
    }
}