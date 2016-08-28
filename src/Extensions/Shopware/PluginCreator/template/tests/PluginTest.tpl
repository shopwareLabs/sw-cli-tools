<?= $configuration->phpFileHeader; ?>

namespace <?= $configuration->name; ?>\Tests;

use <?= $configuration->name; ?>\<?= $configuration->name; ?> as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        '<?= $configuration->name; ?>' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['<?= $configuration->name; ?>'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
