<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\Components\SearchBundleDBAL\Facet;

use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\SearchBundle\FacetResult\BooleanFacetResult;
use Shopware\Bundle\SearchBundleDBAL\FacetHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilderFactory;
use Shopware\Bundle\StoreFrontBundle\Struct;

class <?= $configuration->name; ?>FacetHandler implements FacetHandlerInterface
{
    /**
     * @var QueryBuilderFactory
     */
    private $queryBuilderFactory;

    /**
     * @var \Shopware_Components_Snippet_Manager
     */
    private $snippetManager;

    /**
     * @param QueryBuilderFactory $queryBuilderFactory
     * @param \Shopware_Components_Snippet_Manager $snippetManager
     */
    public function __construct(QueryBuilderFactory $queryBuilderFactory, \Shopware_Components_Snippet_Manager
$snippetManager)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->snippetManager = $snippetManager;
    }

    /**
     * @inheritdoc
     */
    public function supportsFacet(FacetInterface $facet)
    {
        return ($facet instanceof <?= $configuration->name; ?>Facet);
    }

    /**
     * @inheritdoc
     */
    public function generateFacet(
        FacetInterface $facet,
        Criteria $criteria,
        Struct\ShopContextInterface $context
    ) {

        // This will add a new boolean filter. There are plenty of additional filter
        // https://developers.shopware.com/developers-guide/shopware-5-search-bundle/#facetresult
        return new BooleanFacetResult(
            $facet->getName(),
            '<?= $names->under_score_js; ?>_condition',
            $criteria->hasCondition($facet->getName()),
            'Example condition' // use $this->snippetManager for translatable snippets
        );
    }
}
