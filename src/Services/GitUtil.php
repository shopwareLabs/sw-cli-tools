<?php

namespace ShopwareCli\Services;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class GitUtil
{
    /**
     * Number of seconds before the GitUtil will cancel an operation.
     * You might need to increase it at some point, if your checkout / database building takes too long
     */
    const DEFAULT_TIMEOUT = 180;

    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var GitIdentityEnvironment
     */
    private $gitEnv;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output, GitIdentityEnvironment $gitEnv)
    {
        $this->output = $output;
        $this->gitEnv = $gitEnv;
    }

    /**
     * @param  string            $commandline
     * @throws \RuntimeException
     * @return string
     */
    public function run($commandline)
    {
        $commandline = 'git ' . $commandline;

        $process = new Process($commandline, null, $this->gitEnv->getGitEnv());
        $process->setTimeout(self::DEFAULT_TIMEOUT);

        $output = $this->output; // tmp var needed for php < 5.4
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("Command failed. Error Output:\n\n" . $process->getErrorOutput(), $process->getExitCode());
        }

        return $process->getOutput();
    }
}
