<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use ShopwareCli\Application\DependencyInjection;
use ShopwareCli\Services\GitUtil;
use ShopwareCli\Services\ProcessExecutor;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class TimeoutTest extends TestCase
{
    public function getContainer(): ContainerBuilder
    {
        $di = DependencyInjection::createContainer(__DIR__);

        $di->set('output_interface', new NullOutput());

        return $di;
    }

    public function testProcessExecutorTimeout(): void
    {
        putenv('SW_TIMEOUT=999');
        /** @var ProcessExecutor $executor */
        $executor = $this->getContainer()->get('process_executor');

        static::assertSame(999, $executor->getTimeout());
    }

    public function testGitUtilTimeout(): void
    {
        putenv('SW_TIMEOUT=123');
        /** @var GitUtil $util */
        $util = $this->getContainer()->get('git_util');

        static::assertSame(123, $util->getTimeout());
    }
}
