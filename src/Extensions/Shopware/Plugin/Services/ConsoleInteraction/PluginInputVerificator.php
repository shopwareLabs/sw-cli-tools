<?php

namespace Shopware\Plugin\Services\ConsoleInteraction;

use Shopware\Plugin\Struct\Plugin;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Will trigger the PluginColumnRenderer until an valid answer was made. Will then return the corresponding plugin/answer
 *
 * Class PluginInputVerificator
 * @package ShopwareCli\Command\Services
 */
class PluginInputVerificator
{
    /**
     * @var InputInterface
     */
    protected $inputInterface;

    /**
     * @var OutputInterface
     */
    protected $outputInterface;

    /**
     * @var  PluginColumnRenderer
     */
    protected $outputRenderer;

    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @param IoService            $ioService
     * @param PluginColumnRenderer $outputRenderer
     */
    public function __construct(IoService $ioService, PluginColumnRenderer $outputRenderer)
    {
        $this->outputRenderer = $outputRenderer;
        $this->ioService = $ioService;
    }

    /**
     * Ask the user to select one of the given plugins or enter one of $allowedAnswers
     * Will loop until a valid choice was made
     *
     * @param  Plugin[] $plugins
     * @param  string[] $allowedAnswers
     * @return string
     */
    public function selectPlugin($plugins, $allowedAnswers = ['all'])
    {
        while (true) {
            system('clear');
            $this->outputRenderer->show($plugins);

            $question = new Question(
                $this->formatQuestion(count($plugins), $allowedAnswers)
            );

            $response = $this->ioService->ask($question);

            if ($range = $this->getPluginRange($response)) {
                return array_filter(
                    array_map(
                        function ($number) use ($plugins) {
                            return isset($plugins[$number - 1]) ? $plugins[$number - 1] : null;
                        },
                        $range
                    ),
                    function ($plugin) {
                        return $plugin;
                    }
                );
            } elseif (isset($plugins[$response - 1])) {
                return $plugins[$response - 1];
            } elseif (in_array($response, $allowedAnswers)) {
                return $response;
            } else {
                $question = new Question('<error>Invalid answer, hit enter to continue</error>');
                $this->ioService->ask($question);
            }
        }
    }

    /**
     * Format the question for the user
     *
     * @param  integer  $count
     * @param  string[] $allowedAnswers
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
                },
                $allowedAnswers
            );

            return sprintf($template, sprintf('or one of these: %s', implode(', ', $allowedAnswers)));
        }
    }

    /**
     * Check if a range like 12-99 was entered
     *
     * Will return false if not or the numbers from the range as an array
     *
     * @param  string     $userInput
     * @return array|bool
     */
    private function getPluginRange($userInput)
    {
        $pattern = '#(?P<from>[0-9]+)-(?P<to>[0-9]+)#';
        $matches = [];

        preg_match($pattern, $userInput, $matches);

        if (empty($matches) || !isset($matches['from']) || !isset($matches['to'])) {
            return false;
        }

        return range($matches['from'], $matches['to']);
    }
}
