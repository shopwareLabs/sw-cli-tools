<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>;

use Shopware\Components\Plugin;
<?php if ($configuration->hasCommands) { ?>
use Shopware\Components\Console\Application;
use <?= $configuration->name; ?>\Commands\<?= $names->camelCaseModel; ?>;
<?php } ?>
use Symfony\Component\DependencyInjection\ContainerBuilder;
<?php if ($configuration->hasWidget) { ?>
use Shopware\Models\Widget\Widget;
<?php } ?>
<?php if ($configuration->hasModels) { ?>
use Doctrine\ORM\Tools\SchemaTool;
<?php } ?>

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
        parent::install($installContext);
<?php if ($configuration->hasWidget) { ?>
        $repo = $this->container->get('models')->getRepository(\Shopware\Models\Plugin\Plugin::class);
        /** @var \Shopware\Models\Plugin\Plugin $plugin */
        $plugin = $repo->findOneBy([ 'name' => '<?= $configuration->name ?>' ]);

        $widget = new Widget();
        $widget->setName('<?= $names->under_score_js ?>');
        $widget->setPlugin($plugin);

        $plugin->getWidgets()->add($widget);
<?php } ?>

<?php if ($configuration->hasModels) { ?>
        $this->createSchema();
<?php } ?>

    }

    /**
     * Remove widget and remove database schema.
     *
     * @param Plugin\Context\UninstallContext $uninstallContext
     */
    public function uninstall(Plugin\Context\UninstallContext $uninstallContext)
    {
        parent::uninstall($uninstallContext);
<?php if ($configuration->hasWidget) { ?>
        $modelManager = $this->container->get('models');
        $repo = $modelManager->getRepository(Widget::class);

        $widget = $repo->findOneBy([ 'name' => '<?= $names->under_score_js ?>' ]);
        $modelManager->remove($widget);
    $modelManager->flush();
    <?php } ?>

<?php if ($configuration->hasModels) { ?>
        $this->removeSchema();
<?php } ?>
    }

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('<?= $names->under_score_js ?>.plugin_dir', $this->getPath());
        parent::build($container);
    }

<?php if ($configuration->hasModels) { ?>
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
<?php } ?>
}
