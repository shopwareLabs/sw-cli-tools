<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

use Elasticsearch\Client;
use Shopware\Bundle\ESIndexingBundle\IndexFactoryInterface;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\ProductSearchInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;

class Search implements ProductSearchInterface
{
    /**
     * @var ProductSearchInterface
     */
    private $coreService;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var IndexFactoryInterface
     */
    private $indexFactory;

    /**
     * @param Client $client
     * @param ProductSearchInterface $coreService
     * @param IndexFactoryInterface $indexFactory
     */
    public function __construct(
        Client $client,
        ProductSearchInterface $coreService,
        IndexFactoryInterface $indexFactory
    ) {
        $this->coreService = $coreService;
        $this->client = $client;
        $this->indexFactory = $indexFactory;
    }

    public function search(Criteria $criteria, Struct\ProductContextInterface $context)
    {
        $result = $this->coreService->search($criteria, $context);

        return $result;
    }
}
