<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>;

use Shopware\Components\Plugin;
use <?= $configuration->name; ?>\Subscriber\Frontend;
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
            'Enlight_Controller_Front_StartDispatch' => 'onStartDispatch'
        ];
    }

    /**
     * This callback function is triggered at the very beginning of the dispatch process.
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
        $subscribers = [
            new Frontend(),
        ];

        foreach ($subscribers as $subscriber) {
            $this->container->get('application')->Events()->addSubscriber($subscriber);
        }
    }
}