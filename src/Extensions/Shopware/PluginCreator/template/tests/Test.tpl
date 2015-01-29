<?= $configuration->phpFileHeader; ?>

class PluginTest extends Shopware\Components\Test\Plugin\TestCase
{
    protected static $ensureLoadedPlugins = array(
        '<?= $configuration->name; ?>' => array(
        )
    );

    public function setUp()
    {
        parent::setUp();

        $helper = \TestHelper::Instance();
        $loader = $helper->Loader();


        $pluginDir = getcwd() . '/../';

        $loader->registerNamespace(
            'Shopware\\<?= $configuration->name; ?>',
            $pluginDir
        );
    }

    public function testCanCreateInstance()
    {
        /** @var Shopware_Plugins_<?= $configuration->namespace; ?>_<?= $configuration->name; ?>_Bootstrap $plugin */
        $plugin = Shopware()->Plugins()-><?= $configuration->namespace; ?>()-><?= $configuration->name; ?>();

        $this->assertInstanceOf('Shopware_Plugins_<?= $configuration->namespace; ?>_<?= $configuration->name; ?>_Bootstrap', $plugin);
    }
}