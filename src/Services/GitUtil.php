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

class GitUtil
{
    /**
     * Number of seconds before the GitUtil will cancel an operation.
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
     * @var GitIdentityEnvironment
     */
    private $gitEnv;

    /**
     * @param int $timeout
     */
    public function __construct(OutputInterface $output, GitIdentityEnvironment $gitEnv, $timeout)
    {
        $this->timeout = $timeout;
        $this->output = $output;
        $this->gitEnv = $gitEnv;
    }

    /**
     * @param string   $commandline
     * @param int|null $timeout
     */
    public function run($commandline, $timeout = null): string
    {
        $commandline = 'git ' . $commandline;

        $process = new Process($commandline, null, $this->gitEnv->getGitEnv());
        $process->setTimeout($timeout ?: $this->timeout);

        $output = $this->output; // tmp var needed for php < 5.4
        $process->run(static function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("Command \"$commandline\" failed. Error Output:\n\n" . $process->getErrorOutput(), $process->getExitCode());
        }

        return $process->getOutput();
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
