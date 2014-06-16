<?php
namespace ShopwareCli\Services;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ProcessExecutor
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param  string            $commandline
     * @param  bool              $allowFailure
     * @throws \RuntimeException
     * @return int|null
     */
    public function execute($commandline, $allowFailure = false)
    {
        $process = new Process($commandline);
        $output = $this->output; // tmp var needed for php < 5.4
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$allowFailure && !$process->isSuccessful()) {
            throw new \RuntimeException("Command failed. Error Output:\n\n" . $process->getErrorOutput(), $process->getExitCode());
        }

        return $process->getExitCode();
    }
}
