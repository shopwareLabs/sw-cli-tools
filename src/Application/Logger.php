<?php

namespace ShopwareCli\Application;

use ShopwareCli\OutputWriter\OutputWriterInterface;

class Logger
{
    const LEVEL_DEBUG=3;
    const LEVEL_INFO=2;
    const LEVEL_ERROR=1;

    /** @var  OutputWriterInterface */
    protected static $output;

    protected static $logLevel=0;

    public static function setOutputWriter($output)
    {
        self::$output= $output;
    }

    public static function setLogLevel($level)
    {
        self::$logLevel = $level;
    }

    public static function info($message)
    {
        if (self::$logLevel >= self::LEVEL_INFO) {
            self::printMessage($message);
        }
    }

    public static function error($message)
    {
        if (self::$logLevel >= self::LEVEL_ERROR) {
            self::printMessage($message);
        }
    }

    public  static function debug($message)
    {
        if (self::$logLevel >= self::LEVEL_DEBUG) {
            self::printMessage($message);
        }
    }

    /**
     * @param $message
     */
    private static function printMessage($message)
    {
        self::$output->write($message);
    }

}