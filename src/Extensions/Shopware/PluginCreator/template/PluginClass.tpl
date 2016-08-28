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
        // @todo Add your your events here
        return [
            // 'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onFrontendPostDispatch',
        ];
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
