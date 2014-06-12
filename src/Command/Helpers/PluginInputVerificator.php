<?php

namespace ShopwareCli\Command\Helpers;

use ShopwareCli\Config;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Will trigger the PluginColumnRenderer until an valid answer was made. Will then return the corresponding plugin/answer
 *
 * Class PluginInputVerificator
 * @package ShopwareCli\Command\Helpers
 */
class PluginInputVerificator
{
    protected $inputInterface;
    protected $outputInterface;

    /** @var  PluginColumnRenderer */
    protected $outputRenderer;

    protected $small;
    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper, $config, $small = false)
    {
        $this->inputInterface  = $input;
        $this->outputInterface = $output;
        $this->questionHelper  = $questionHelper;

        $this->small = $small;
        $this->outputRenderer = new PluginColumnRenderer($input, $output, $config, $small);
    }

    /**
     * Ask the user to select one of the given plugins or enter one of $allowedAnswers
     * Will loop until a valid choice was made
     *
     * @param $plugins
     * @param array $allowedAnswers
     * @return string
     */
    public function selectPlugin($plugins, $allowedAnswers = array('all'))
    {
        while (true) {
            system('clear');
            $this->outputRenderer->show($plugins);

            $question = new Question(
                $this->formatQuestion(count($plugins), $allowedAnswers)
            );

            $response = $this->questionHelper->ask(
                $this->inputInterface,
                $this->outputInterface,
                $question
            );

            if (isset($plugins[$response - 1])) {
                return $plugins[$response - 1];
            } elseif (in_array($response, $allowedAnswers)) {
                return $response;
            } else {
                $question = new Question('<error>Invalid answer, hit enter to continue</error>');
                $this->questionHelper->ask($this->inputInterface, $this->outputInterface, $question);
            }
        }
    }

    /**
     * Format the question for the user
     *
     * @param $count
     * @param $allowedAnswers
     * @return string
     */
    private function formatQuestion($count, $allowedAnswers)
    {
        $template = "\n<question>Which plugin(s) do you want to install?</question> Type <comment>1-{$count}</comment> %s ";

        if (empty($allowedAnswers)) {
            return sprintf($template, '');
        } elseif (count($allowedAnswers) == 1) {
            return sprintf($template, sprintf('or "%s"', $allowedAnswers[0]));
        } else {
            $allowedAnswers = array_map(
                function ($option) {
                    return sprintf('"<comment>%s</comment>"', $option);
                }, $allowedAnswers
            );

            return sprintf($template, sprintf('or one of these: %s', implode(', ', $allowedAnswers)));
        }
    }
}
