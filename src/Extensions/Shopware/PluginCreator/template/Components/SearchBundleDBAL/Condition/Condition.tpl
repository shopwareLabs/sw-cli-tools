<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

<?php if ($configuration->isLegacyPlugin) { ?>
namespace <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\Components\SearchBundleDBAL\Condition;
<?php } else { ?>
namespace <?= $configuration->name; ?>\Components\SearchBundleDBAL\Condition;
<?php } ?>

use Shopware\Bundle\SearchBundle\ConditionInterface;

class <?= $configuration->name; ?>Condition implements ConditionInterface
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
