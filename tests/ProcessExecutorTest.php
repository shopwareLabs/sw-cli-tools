<?php

namespace ShopwareCli\tests;

use ShopwareCli\Services\ProcessExecutor;
use Symfony\Component\Console\Output\BufferedOutput;

class ProcessExecutorTest extends \PHPUnit_Framework_TestCase
{
    public function testCliToolGateway()
    {
        $output = new BufferedOutput();
        $executor = new ProcessExecutor($output, 60);

        $exitCode = $executor->execute('true');
        $this->assertEquals(0, $exitCode);
        $this->assertEquals('', $output->fetch());

        $exitCode = $executor->execute('echo foo');
        $this->assertEquals(0, $exitCode);
        $this->assertEquals("foo\n", $output->fetch());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Command failed. Error Output:
     * @expectedExceptionCode 1
     */
    public function testFailedCommand()
    {
        $output = new BufferedOutput();
        $executor = new ProcessExecutor($output, 60);

        $executor->execute('false');
    }

    public function testFailedCommand2()
    {
        $output = new BufferedOutput();
        $executor = new ProcessExecutor($output, 60);

        $expectedOutput = "ls: cannot access /no-such-file: No such file or directory\n";
        try {
            $executor->execute('LC_ALL=C ls /no-such-file');
        } catch (\Exception $e) {
            $this->assertEquals(2, $e->getCode());
            $this->assertContains($expectedOutput, $e->getMessage());
            $this->assertEquals($expectedOutput, $output->fetch());

            return;
        }

        $this->fail("Executor should throw exception on failed command");
    }

    public function testAllowFailingCmmand()
    {
        $output = new BufferedOutput();
        $executor = new ProcessExecutor($output, 60);

        $expectedOutput = "ls: cannot access /no-such-file: No such file or directory\n";

        $exitCode = $executor->execute('LC_ALL=C ls /no-such-file', null, true);

        $this->assertEquals(2, $exitCode);
        $this->assertEquals($expectedOutput, $output->fetch());
    }

    /**
     * @group slow
     * @expectedException \Symfony\Component\Process\Exception\ProcessTimedOutException
     * @expectedExceptionMessage The process "sleep 2" exceeded the timeout of 1 seconds.
     */
    public function testTimeout()
    {
        $output = new BufferedOutput();
        $executor = new ProcessExecutor($output, 1);


        $executor->execute('sleep 2', null, true);
    }
}
