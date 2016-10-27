<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

<?php if ($configuration->hasElasticSearch) { ?>
use Shopware\Components\Model\ModelManager;
use <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\ESIndexingBundle\DataIndexer;
use <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\ESIndexingBundle\Mapping;
use <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\ESIndexingBundle\Provider;
use <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\ESIndexingBundle\Settings;
use <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\ESIndexingBundle\Synchronizer;
use <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\SearchBundleES\Search;
<?php } ?>

/**
 * The Bootstrap class is the main entry point of any shopware plugin.
 *
 * Short function reference
 * - install: Called a single time during (re)installation. Here you can trigger install-time actions like
 *   - creating the menu
 *   - creating attributes
 *   - creating database tables
 *   You need to return "true" or array('success' => true, 'invalidateCache' => array()) in order to let the installation
 *   be successful
 *
 * - update: Triggered when the user updates the plugin. You will get passes the former version of the plugin as param
 *   In order to let the update be successful, return "true"
 *
 * - uninstall: Triggered when the plugin is reinstalled or uninstalled. Clean up your tables here.
 */
class Shopware_Plugins_<?= $configuration->namespace; ?>_<?= $configuration->name; ?>_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    public function getVersion() {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .'plugin.json'), true);
        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    public function getLabel()
    {
        return '<?= $configuration->name; ?>';
    }

    public function uninstall()
    {
<?php if ($configuration->hasModels) { ?>
        $this->registerCustomModels();

        $em = $this->Application()->Models();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata('Shopware\CustomModels\<?= $configuration->name; ?>\<?= $names->camelCaseModel; ?>')
        );
        $tool->dropSchema($classes);

<?php } ?>
        return true;
    }

    public function update($oldVersion)
    {
        return true;
    }

    public function install()
    {
        if (!$this->assertMinimumVersion('<?= $configuration->pluginConfig['minimumVersion']; ?>')) {
            throw new \RuntimeException('At least Shopware <?= $configuration->pluginConfig['minimumVersion']; ?> is required');
        }
<?php if ($configuration->hasWidget) { ?>

        $this->createWidget('<?= $names->under_score_js; ?>');
<?php } ?>

<?php if ($configuration->hasBackend) { ?>

        $this->createMenuItem(array(
            'label' => '<?= $configuration->name; ?>',
            'controller' => '<?= $configuration->name; ?>',
            'class' => 'sprite-application-block',
            'action' => 'Index',
            'active' => 1,
            'parent' => $this->Menu()->findOneBy(array('label' => 'Marketing'))
        ));
<?php } ?>

        $this->subscribeEvent(
            'Enlight_Controller_Front_DispatchLoopStartup',
            'onStartDispatch'
        );

<?php if ($configuration->hasCommands) { ?>

        $this->subscribeEvent(
            'Shopware_Console_Add_Command',
            'onAddConsoleCommand'
        );
<?php } ?>
<?php if ($configuration->hasModels) { ?>

<?php if ($configuration->hasElasticSearch) { ?>
        $this->subscribeElasticSearchEvents();
<?php } ?>

        $this->updateSchema();
<?php } ?>
<?php if ($configuration->hasFrontend || $configuration->hasBackend) { ?>
        return array('success' => true, 'invalidateCache' => array('frontend', 'backend'));
<?php } else { ?>
        return true;
<?php } ?>
    }

<?php if ($configuration->hasCommands) { ?>
    /**
     * Callback function of the console event subscriber. Register your console commands here.
     */
    public function onAddConsoleCommand(Enlight_Event_EventArgs $args)
    {
        $this->registerMyComponents();

        // You can easily add more commands here
        return new \Doctrine\Common\Collections\ArrayCollection(array(
            new \<?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\Commands\<?= $names->camelCaseModel; ?>()
        ));
    }
<?php } ?>
<?php if ($configuration->hasModels) { ?>

    /**
     * Creates the database scheme from an existing doctrine model.
     *
     * Will remove the table first, so handle with care.
     */
    protected function updateSchema()
    {
        $this->registerCustomModels();

        $em = $this->Application()->Models();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata('Shopware\CustomModels\<?= $configuration->name; ?>\<?= $names->camelCaseModel; ?>')
        );

        try {
            $tool->dropSchema($classes);
        } catch (Exception $e) {
            //ignore
        }
        $tool->createSchema($classes);
    }
<?php } ?>

    /**
     * This callback function is triggered at the very beginning of the dispatch process and allows
     * us to register additional events on the fly. This way you won't ever need to reinstall you
     * plugin for new events - any event and hook can simply be registered in the event subscribers
     */
    public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
        $this->registerMyComponents();
<?php if ($configuration->hasModels) { ?>
        $this->registerCustomModels();<?php } ?>
<?php if ($configuration->hasApi) { ?>$this->registerApiComponent();<?php } ?>
<?php if ($configuration->hasFrontend || $configuration->hasBackend || $configuration->hasWidget) { ?>
        $this->registerMyTemplateDir();
        $this->registerMySnippets();
<?php } ?>


        $subscribers = array(
<?php if ($configuration->hasFrontend || $configuration->hasBackend || $configuration->hasWidget || $configuration->hasApi) { ?>
            new \<?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\Subscriber\ControllerPath(),
<?php } ?><?php if ($configuration->hasFilter) { ?>
            new \<?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\Subscriber\SearchBundle(),
<?php } ?>
            new \<?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\Subscriber\Frontend()
        );

        foreach ($subscribers as $subscriber) {
            $this->Application()->Events()->addSubscriber($subscriber);
        }
    }
<?php if ($configuration->hasFrontend || $configuration->hasBackend || $configuration->hasWidget) { ?>

    /**
     * Registers the template directory, needed for templates in frontend an backend
     */
    public function registerMyTemplateDir()
    {
        Shopware()->Template()->addTemplateDir($this->Path() . 'Views');
    }

    /**
     * Registers the snippet directory, needed for backend snippets
     */
    public function registerMySnippets()
    {
        $this->Application()->Snippets()->addConfigDir(
            $this->Path() . 'Snippets/'
        );
    }
<?php } ?>
<?php if ($configuration->hasApi) { ?>

    public function registerApiComponent()
    {
        $this->Application()->Loader()->registerNamespace(
            '<?= $configuration->pluginConfig['namespace']; ?>\Components',
            $this->Path() . 'Components/'
        );
    }
<?php } ?>

    public function registerMyComponents()
    {
        $this->Application()->Loader()->registerNamespace(
            '<?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>',
            $this->Path()
        );
    }

<?php if ($configuration->hasElasticSearch) { ?>
    private function subscribeElasticSearchEvents()
    {
        $this->subscribeEvent('Enlight_Bootstrap_InitResource_<?= $names->under_score_js ?>_search.indexer', 'registerIndexerService');
        $this->subscribeEvent('Shopware_ESIndexingBundle_Collect_Indexer', 'addIndexer');
        $this->subscribeEvent('Shopware_ESIndexingBundle_Collect_Mapping', 'addMapping');
        $this->subscribeEvent('Shopware_ESIndexingBundle_Collect_Synchronizer', 'addSynchronizer');
        $this->subscribeEvent('Enlight_Bootstrap_AfterInitResource_shopware_search.product_search', 'decorateProductSearch');
        $this->subscribeEvent('Shopware_ESIndexingBundle_Collect_Settings', 'addSettings');
    }

    public function addSettings()
    {
        return new Settings();
    }

    public function registerIndexerService()
    {
        return new DataIndexer(
            $this->get('dbal_connection'),
            $this->get('shopware_elastic_search.client'),
            new Provider($this->get('dbal_connection'))
        );
    }

    public function addIndexer()
    {
        return $this->get('<?= $names->under_score_js ?>_search.indexer');
    }

    public function addMapping()
    {
        return new Mapping($this->get('shopware_elastic_search.field_mapping'));
    }

    public function addSynchronizer()
    {
        return new Synchronizer(
            $this->get('<?= $names->under_score_js ?>_search.indexer'),
            $this->get('dbal_connection')
        );
    }

    public function decorateProductSearch()
    {
        $service = new Search(
            $this->get('shopware_elastic_search.client'),
            $this->get('shopware_search.product_search'),
            $this->get('shopware_elastic_search.index_factory')
        );
        Shopware()->Container()->set('shopware_search.product_search', $service);
    }
<?php } ?>



}
