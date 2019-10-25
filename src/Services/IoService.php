<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Class IoService
 */
class IoService
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param QuestionHelper  $questionHelper
     */
    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
    }

    /**
     * Returns true if the input is interactive
     *
     * @return bool
     */
    public function isInteractive()
    {
        return $this->input->isInteractive();
    }

    /**
     * Returns true if output is quiet
     *
     * @return bool
     */
    public function isQuiet()
    {
        return $this->output->getVerbosity() === OutputInterface::VERBOSITY_QUIET;
    }

    /**
     * Return true if output is verbose (or more verbose)
     *
     * @return bool
     */
    public function isVerbose()
    {
        return $this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
    }

    /**
     * Return true if output is ver verbose (or debug)
     *
     * @return bool
     */
    public function isVeryVerbose()
    {
        return $this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE;
    }

    /**
     * Return true if output is debug
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG;
    }

    /**
     * Write a message to STDOUT without trailing newline
     *
     * @param string $message
     */
    public function write($message)
    {
        $this->output->write($message);
    }

    /**
     * Write a message to STDOUT with trailing newline
     *
     * @param $message
     */
    public function writeln($message)
    {
        $this->output->write($message, true);
    }

    /**
     * Ask a $question
     *
     * @param string|Question $question
     * @param null            $default
     *
     * @return string
     */
    public function ask($question, $default = null, $hidden = false)
    {
        $question = $question instanceof Question ? $question : new Question($question, $default);

        if ($hidden) {
            $question->setHidden(true);
            $question->setHiddenFallback(false);
        }

        return $this->questionHelper->ask($this->input, $this->output, $question);
    }

    /**
     * Creates a table
     *
     * @param array|null $headers
     * @param array|null $results
     *
     * @return Table
     */
    public function createTable($headers = null, $results = null)
    {
        $tableObject = new Table($this->output);

        if ($headers) {
            $tableObject->setHeaders($headers);
        }

        if ($results) {
            $tableObject->setRows($results);
        }

        return $tableObject;
    }

    /**
     * Ask for confirmation
     *
     * @param string|Question $question
     * @param null            $default
     *
     * @return string
     */
    public function askConfirmation($question, $default = null)
    {
        $question = $question instanceof ConfirmationQuestion
            ? $question
            : new ConfirmationQuestion($question, $default);

        return $this->questionHelper->ask($this->input, $this->output, $question);
    }

    /**
     * Ask a question and validate the result
     *
     * @param string|Question $question
     * @param bool|callable   $validator
     * @param bool|int        $attempts
     * @param null|string     $default
     *
     * @return string
     */
    public function askAndValidate($question, $validator = false, $attempts = false, $default = null, $hidden = false)
    {
        $question = $question instanceof Question ? $question : new Question($question, $default);

        if ($attempts) {
            $question->setMaxAttempts($attempts);
        }

        if ($validator) {
            $question->setValidator($validator);
        }

        if ($hidden) {
            $question->setHidden(true);
            $question->setHiddenFallback(false);
        }

        return $this->questionHelper->ask($this->input, $this->output, $question);
    }

    /**
     * @param int $max Maximum steps (0 if unknown)
     *
     * @return ProgressBar
     */
    public function createProgressBar($max = 0)
    {
        return new ProgressBar($this->output, $max);
    }

    /**
     * Clears the screen in the terminal
     */
    public function cls()
    {
        if (!$this->input->isInteractive()) {
            return;
        }

        // http://en.wikipedia.org/wiki/ANSI_escape_code
        $this->output->write(chr(27) . '[2J'); // ED – Erase Display
        $this->output->write(chr(27) . '[1;1H'); // CUP – Set Cursor Position to upper left
    }
}
