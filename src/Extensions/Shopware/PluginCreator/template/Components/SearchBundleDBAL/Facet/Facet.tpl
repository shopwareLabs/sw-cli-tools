<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace Shopware\<?= $configuration->name; ?>\Components\SearchBundleDBAL\Facet;

use Shopware\Bundle\SearchBundle\FacetInterface;

class <?= $configuration->name; ?>Facet implements FacetInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return '<?= $names->under_score_js; ?>';
    }

    /**
     * @inheritdoc
     */
    function jsonSerialize()
    {
        return get_object_vars($this);
    }
}