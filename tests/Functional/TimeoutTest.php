<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class TimeoutTest extends PHPUnit_Framework_TestCase
{
    public function getContainer()
    {
        $di = \ShopwareCli\Application\DependencyInjection::createContainer(__DIR__);

        $di->set('output_interface', new \Symfony\Component\Console\Output\NullOutput());

        return $di;
    }

    public function testProcessExecutorTimeout()
    {
        putenv('SW_TIMEOUT=999');
        $executor = $this->getContainer()->get('process_executor');
        $this->assertAttributeEquals('999', 'timeout', $executor);
    }

    public function testGitUtilTimeout()
    {
        putenv('SW_TIMEOUT=123');
        $util = $this->getContainer()->get('git_util');
        $this->assertAttributeEquals('123', 'timeout', $util);
    }
}
