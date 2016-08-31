<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>;

use Shopware\Components\Plugin;
<?php if ($configuration->hasCommands) { ?>
use Shopware\Components\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Models\Widget\Widget;
use Doctrine\ORM\Tools\SchemaTool;
use <?= $configuration->name; ?>\Commands\<?= $names->camelCaseModel; ?>;
<?php } ?>
use Enlight_Event_EventArgs;

/**
 * Shopware-Plugin <?= $configuration->name; ?>.
 */
class <?= $configuration->name; ?> extends Plugin
{
    /**
     * Adds the widget to the database and creates the database schema.
     *
     * @param Plugin\Context\InstallContext $installContext
     */
    public function install(Plugin\Context\InstallContext $installContext)
    {
        $repo = $this->container->get('models')->getRepository(\Shopware\Models\Plugin\Plugin::class);
        /** @var \Shopware\Models\Plugin\Plugin $plugin */
        $plugin = $repo->findOneBy([ 'name' => '<?= $configuration->name ?>' ]);

        $widget = new Widget();
        $widget->setName('<?= $names->under_score_js ?>');
        $widget->setPlugin($plugin);

        $plugin->getWidgets()->add($widget);

        $this->createSchema();
    }

    /**
     * Remove widget and remove database schema.
     *
     * @param Plugin\Context\UninstallContext $uninstallContext
     */
    public function uninstall(Plugin\Context\UninstallContext $uninstallContext)
    {
        $modelManager = $this->container->get('models');
        $repo = $modelManager->getRepository(Widget::class);

        $widget = $repo->findOneBy([ 'name' => '<?= $names->under_score_js ?>' ]);
        $modelManager->remove($widget);
        $modelManager->flush();

        $this->removeSchema();
    }

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('<?= $names->under_score_js ?>.plugin_dir', $this->getPath());
        parent::build($container);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
<?php if ($configuration->hasApi) { ?>
        return [
            'Enlight_Controller_Front_DispatchLoopStartup' => 'onStartDispatch',
        ];
<?php } else { ?>
        // @todo Add your your events here
        return [
            // 'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onFrontendPostDispatch',
        ];
<?php } ?>
    }
<?php if ($configuration->hasCommands) { ?>
    /**
     * Register PluginCommands
     *
     * @param Application $application
     */
    public function registerCommands(Application $application)
    {
        $application->add(new <?= $names->camelCaseModel; ?>());
    }
<?php } ?>
<?php if ($configuration->hasApi) { ?>

    /**
     * This callback function is triggered at the very beginning of the dispatch process and allows
     * us to register additional events on the fly. This way you won't ever need to reinstall you
     * plugin for new events - any event and hook can simply be registerend in the event subscribers
     */
    public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
        $this->registerApiComponents();
    }

    /**
     * Register your API-Resources
     */
    private function registerApiComponents()
    {
        $this->container->get('application')->Loader()->registerNamespace(
            '<?= $configuration->pluginConfig['namespace']; ?>\Components\Api',
            $this->getPath() . 'Components/Api/'
        );
    }
<?php } ?>

    /**
     * creates database tables on base of doctrine models
     */
    private function createSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));
        $classes = [
            $this->container->get('models')->getClassMetadata(\<?= $configuration->name ?>\Models\<?= $names->camelCaseModel ?>::class)
        ];
        $tool->createSchema($classes);
    }

    private function removeSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));
        $classes = [
            $this->container->get('models')->getClassMetadata(\<?= $configuration->name ?>\Models\<?= $names->camelCaseModel ?>::class)
        ];
        $tool->dropSchema($classes);
    }
}
