<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name ?>\Components\ESIndexingBundle;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\ESIndexingBundle\Struct\ShopIndex;
use Shopware\Bundle\ESIndexingBundle\SynchronizerInterface;

class Synchronizer implements SynchronizerInterface
{
    /**
     * @var DataIndexer
     */
    private $indexer;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param DataIndexer $indexer
     * @param Connection $connection
     */
    public function __construct(DataIndexer $indexer, Connection $connection)
    {
        $this->indexer = $indexer;
        $this->connection = $connection;
    }

    public function synchronize(ShopIndex $shopIndex, $backlogs)
    {
    }
}
