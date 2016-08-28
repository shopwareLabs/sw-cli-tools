<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>;

use Shopware\Components\Plugin;
<?php if ($configuration->hasCommands) { ?>
use Shopware\Components\Console\Application;
use <?= $configuration->name; ?>\Commands\<?= $names->camelCaseModel; ?>;
<?php } ?>
use Enlight_Event_EventArgs;

/**
 * Shopware-Plugin <?= $configuration->name; ?>.
 */
class <?= $configuration->name; ?> extends Plugin
{
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
}
