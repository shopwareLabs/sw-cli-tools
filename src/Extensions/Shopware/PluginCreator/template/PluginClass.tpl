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
        return [
            'Enlight_Controller_Front_DispatchLoopStartup' => 'onStartDispatch'
        ];
    }

    /**
     * This callback function is triggered at the very beginning of the dispatch process.
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
        $subscribers = [];

        foreach ($subscribers as $subscriber) {
            $this->container->get('application')->Events()->addSubscriber($subscriber);
        }
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
}
