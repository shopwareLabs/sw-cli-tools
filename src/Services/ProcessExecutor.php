<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @param int $timeout
     */
    public function __construct(OutputInterface $output, $timeout)
    {
        $this->timeout = $timeout;
        $this->output = $output;
    }

    public function execute(string $commandline, ?string $cwd = null, bool $allowFailure = false, ?int $timeout = null): ?int
    {
        $process = Process::fromShellCommandline($commandline, $cwd);
        $process->setTimeout($timeout ?: $this->timeout);

        $output = $this->output; // tmp var needed for php < 5.4
        $process->run(static function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$allowFailure && !$process->isSuccessful()) {
            throw new \RuntimeException("Command failed. Error Output:\n\n" . $process->getErrorOutput(), $process->getExitCode());
        }

        return $process->getExitCode();
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
