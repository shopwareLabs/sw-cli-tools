<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional;

use PHPUnit\Framework\TestCase;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class IoServiceTest extends TestCase
{
    /**
     * @var ArrayInput
     */
    private $input;

    /**
     * @var BufferedOutput
     */
    private $output;

    public function testShouldClearScreenInInteractiveMode(): void
    {
        $SUT = $this->createSUT();

        $this->input->setInteractive(true);

        $SUT->cls();

        static::assertEquals(
            \chr(27) . '[2J' . \chr(27) . '[1;1H',
            $this->output->fetch()
        );
    }

    public function testShouldNotClearScreenInNotInteractiveMode(): void
    {
        $SUT = $this->createSUT();

        $this->input->setInteractive(false);

        $SUT->cls();

        static::assertEquals(
            '',
            $this->output->fetch()
        );
    }

    private function createSUT(): IoService
    {
        $this->input = new ArrayInput([]);
        $this->output = new BufferedOutput();
        $questionHelper = new QuestionHelper();

        return new IoService(
            $this->input,
            $this->output,
            $questionHelper
        );
    }
}
