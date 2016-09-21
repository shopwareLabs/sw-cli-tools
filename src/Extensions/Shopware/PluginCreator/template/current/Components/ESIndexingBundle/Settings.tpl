<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name ?>\Components\ESIndexingBundle;

use Shopware\Bundle\ESIndexingBundle\SettingsInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

class Settings implements SettingsInterface
{
    /**
     * @param Shop $shop
     * @return array|null
     */
    public function get(Shop $shop)
    {
    }
}
