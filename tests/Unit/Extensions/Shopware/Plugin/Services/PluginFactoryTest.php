<?php
declare(strict_types=1);
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Shopware\Plugin\Services\PluginFactory;

class PluginFactoryTest extends TestCase
{
    /**
     * @dataProvider sw6PluginDataProvider
     */
    public function testGetPluginIsSw6Plugin(string $name, string $sshUrl, string $httpUrl, string $repoName, ?string $repoType = null): void
    {
        $plugin = PluginFactory::getPlugin($name, $sshUrl, $httpUrl, $repoName, $repoType);

        static::assertTrue($plugin->isShopware6);
    }

    /**
     * @dataProvider notSw6PluginDataProvider
     */
    public function testGetPluginIsNotSw6Plugin(string $name, string $sshUrl, string $httpUrl, string $repoName, ?string $repoType = null): void
    {
        $plugin = PluginFactory::getPlugin($name, $sshUrl, $httpUrl, $repoName, $repoType);

        static::assertFalse($plugin->isShopware6);
    }

    public function sw6PluginDataProvider(): Generator
    {
        foreach (PluginFactory::SW6_PLUGIN_PATHS as $path) {
            yield 'Plugin is SW6 - ' . $path => [
                'name' => 'frontend_myPluginName',
                'sshUrl' => 'git@foo.bar.com:' . $path . '/myPlugin.git',
                'httpUrl' => 'https://mygitlabserver.com/shopware/6/enterprise/myPlugin.git',
                'repoName' => 'myPlugin',
                'repoType' => null,
            ];
        }
    }

    public function notSw6PluginDataProvider(): Generator
    {
        yield 'Plugin is not SW6 - custom namespace' => [
            'name' => 'frontend_myPluginName',
            'sshUrl' => 'git@foo.bar.com:my/custom/namespace/myPlugin.git',
            'httpUrl' => 'https://mygitlabserver.com/my/custom/namespace/myPlugin.git',
            'repoName' => 'myPlugin',
            'repoType' => null,
        ];
    }
}
