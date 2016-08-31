<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

<?php if ($configuration->isLegacyPlugin) { ?>
namespace <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\Components\SearchBundleDBAL\Facet;
<?php } else { ?>
namespace <?= $configuration->name; ?>\Components\SearchBundleDBAL\Facet;
<?php } ?>

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
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
