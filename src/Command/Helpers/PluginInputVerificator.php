<?php

namespace ShopwareCli\Command\Helpers;

use ShopwareCli\Config;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

    protected $dialog;

    protected $small;

    public function __construct(InputInterface $input, OutputInterface $output, $dialog, $config, $small = false)
    {
        $this->inputInterface = $input;
        $this->outputInterface = $output;
        $this->dialog = $dialog;

        $this->small = $small;

        $this->outputRenderer = new PluginColumnRenderer($input, $output, $config, $small);
    }

    public function selectPlugin($plugins, $allowedAnswers = array('all'))
    {
        while (true) {
            system('clear');
            $this->outputRenderer->show($plugins);

            $response = $this->dialog->ask(
                $this->outputInterface,
                $this->formatQuestion(count($plugins), $allowedAnswers)
            );


            if (isset($plugins[$response - 1])) {
                return $plugins[$response - 1];
            } elseif (in_array($response, $allowedAnswers)) {
                return $response;
            } else {
                $this->dialog->ask($this->outputInterface, '<error>Invalid answer, hit enter to continue</error>');
            }
        }
    }

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