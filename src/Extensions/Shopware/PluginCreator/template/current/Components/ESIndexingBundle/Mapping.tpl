<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name ?>\Components\ESIndexingBundle;

use Shopware\Bundle\ESIndexingBundle\FieldMappingInterface;
use Shopware\Bundle\ESIndexingBundle\MappingInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

class Mapping implements MappingInterface
{
    /**
     * @var FieldMappingInterface
     */
    private $fieldMapping;

    /**
     * @param FieldMappingInterface $fieldMapping
     */
    public function __construct(FieldMappingInterface $fieldMapping)
    {
        $this->fieldMapping = $fieldMapping;
    }

    /**
     * @return string
     */
    public function getType()
    {
    }

    /**
     * @param Shop $shop
     * @return array
     */
    public function get(Shop $shop)
    {
    }
}
