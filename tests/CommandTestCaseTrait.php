<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

trait CommandTestCaseTrait
{
    use ApplicationTestCaseTrait;

    /**
     * Accepts command line argument like "plugin:create", outputs every console output as one item
     * in a numeric indexed array.
     *
     * @param string $command
     */
    public function runCommand($command): array
    {
        $fp = \tmpfile();
        $input = new StringInput($command);
        $output = new StreamOutput($fp);

        self::getApplication()->doRun($input, $output);

        $consoleOutput = $this->readConsoleOutput($fp);

        return \explode(\PHP_EOL, $consoleOutput);
    }

    private function readConsoleOutput($fp): string
    {
        \fseek($fp, 0);
        $output = '';
        while (!\feof($fp)) {
            $output = \fread($fp, 4096);
        }
        \fclose($fp);

        return $output;
    }
}
