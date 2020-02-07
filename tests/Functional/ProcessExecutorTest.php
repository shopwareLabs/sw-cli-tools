<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional;

use PHPUnit\Framework\TestCase;
use ShopwareCli\Services\ProcessExecutor;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class ProcessExecutorTest extends TestCase
{
    public function testCliToolGateway(): void
    {
        $output = new BufferedOutput();
        $executor = new ProcessExecutor($output, 60);

        $exitCode = $executor->execute('true');
        static::assertEquals(0, $exitCode);
        static::assertEquals('', $output->fetch());

        $exitCode = $executor->execute('echo foo');
        static::assertEquals(0, $exitCode);
        static::assertEquals("foo\n", $output->fetch());
    }

    public function testFailedCommand(): void
    {
        $output = new BufferedOutput();
        $executor = new ProcessExecutor($output, 60);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Command failed. Error Output:');
        $this->expectExceptionCode(1);
        $executor->execute('false');
    }

    public function testFailedCommand2(): void
    {
        $output = new BufferedOutput();
        $executor = new ProcessExecutor($output, 60);

        $expectedOutput = "No such file or directory\n";
        try {
            $executor->execute('LC_ALL=C ls /no-such-file');
        } catch (\Exception $e) {
            static::assertEquals(2, $e->getCode());
            static::assertStringContainsString($expectedOutput, $e->getMessage());
            static::assertStringContainsString($expectedOutput, $output->fetch());

            return;
        }

        static::fail('Executor should throw exception on failed command');
    }

    public function testAllowFailingCommand(): void
    {
        $output = new BufferedOutput();
        $executor = new ProcessExecutor($output, 60);

        $expectedOutput = "No such file or directory\n";

        $exitCode = $executor->execute('LC_ALL=C ls /no-such-file', null, true);

        static::assertEquals(2, $exitCode);
        static::assertStringContainsString($expectedOutput, $output->fetch());
    }

    /**
     * @group slow
     */
    public function testTimeout(): void
    {
        $output = new BufferedOutput();
        $executor = new ProcessExecutor($output, 1);

        $this->expectException(ProcessTimedOutException::class);
        $this->expectExceptionMessage('The process "sleep 2" exceeded the timeout of 1 seconds.');
        $executor->execute('sleep 2', null, true);
    }
}
