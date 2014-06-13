<?php

namespace ShopwareCli\Services;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Class IoService
 * @package ShopwareCli\Services
 */
class IoService
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;
    /**
     * @var \Symfony\Component\Console\Helper\HelperSet
     */
    private $helper;

    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helper)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helper = $helper;
    }

    /**
     * Write a message to STDOUT
     *
     * @param $message
     * @param bool $newLine
     */
    public function write($message, $newLine = true)
    {
        $this->output->write($message, $newLine);
    }

    /**
     * Ask a $question
     *
     * @param $question
     * @param null $default
     * @return string
     */
    public function ask($question, $default = null)
    {
        $question = $question instanceof Question ? $question : new Question($question, $default);

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->helper->get('question');

        return $questionHelper->ask($this->input, $this->output, $question);
    }

    /**
     * Ask for confirmation
     *
     * @param $question
     * @param null $default
     * @return string
     */
    public function askConfirmation($question, $default = null)
    {
        $question = $question instanceof ConfirmationQuestion ? $question : new ConfirmationQuestion($question, $default);

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->helper->get('question');
        return $questionHelper->ask($this->input, $this->output, $question);
    }

    /**
     * Ask a question and validate the result
     *
     * @param $question
     * @param bool $validator
     * @param bool $attempts
     * @param null $default
     * @return string
     */
    public function askAndValidate($question, $validator = false, $attempts = false, $default = null)
    {
        $question = $question instanceof Question ? $question : new Question($question, $default);

        if ($attempts) {
            $question->setMaxAttempts($attempts);
        }

        if ($validator) {
            $question->setValidator($validator);
        }

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->helper->get('question');

        return $questionHelper->ask($this->input, $this->output, $question);
    }
}