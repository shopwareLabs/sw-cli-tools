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
    public function selectPlugin($plugins, $allowedAnswers = array('all'))
    {
        while (true) {
            system('clear');
            $this->outputRenderer->show($plugins);

            $question = new Question(
                $this->formatQuestion(count($plugins), $allowedAnswers)
            );

            $response = $this->ioService->ask($question);

            if (isset($plugins[$response - 1])) {
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
}
