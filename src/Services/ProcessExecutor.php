<?php
namespace ShopwareCli\Services;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ProcessExecutor
{
    /**
     * Number of seconds before the ProcessExecutor will cancel an operation.
     * You might need to increase it at some point, if your checkout / database building takes too long
     *
     * @var int
     */
    private $timeout;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     * @param int $timeout
     */
    public function __construct(OutputInterface $output, $timeout)
    {
        $this->timeout = $timeout;
        $this->output = $output;
    }

    /**
     * @param  string $commandline
     * @param  string $cwd
     * @param  bool $allowFailure
     * @param int|null $timeout
     *
     * @return int|null
     */
    public function execute($commandline, $cwd = null, $allowFailure = false, $timeout = null)
    {
        $process = new Process($commandline, $cwd);
        $process->setTimeout($timeout ?: $this->timeout);

        $output = $this->output; // tmp var needed for php < 5.4
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$allowFailure && !$process->isSuccessful()) {
            throw new \RuntimeException("Command failed. Error Output:\n\n".$process->getErrorOutput(), $process->getExitCode());
        }

        return $process->getExitCode();
    }
}
