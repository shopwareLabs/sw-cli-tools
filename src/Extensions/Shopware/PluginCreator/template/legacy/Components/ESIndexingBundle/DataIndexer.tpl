<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\ESIndexingBundle;

use Doctrine\DBAL\Connection;
use Elasticsearch\Client;
use Shopware\Bundle\ESIndexingBundle\Console\ProgressHelperInterface;
use Shopware\Bundle\ESIndexingBundle\DataIndexerInterface;
use Shopware\Bundle\ESIndexingBundle\Struct\ShopIndex;

class DataIndexer implements DataIndexerInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param Connection $connection
     * @param Client $client
     * @param Provider $provider
     */
    public function __construct(
        Connection $connection,
        Client $client,
        Provider $provider
    ) {
        $this->connection = $connection;
        $this->client = $client;
        $this->provider = $provider;
    }

    /**
     * @param ShopIndex $index
     * @param ProgressHelperInterface $progress
     */
    public function populate(ShopIndex $index, ProgressHelperInterface $progress)
    {
    }

    /**
     * @param ShopIndex $index
     * @param int[] $ids
     */
    public function index(ShopIndex $index, $ids)
    {
    }
}
