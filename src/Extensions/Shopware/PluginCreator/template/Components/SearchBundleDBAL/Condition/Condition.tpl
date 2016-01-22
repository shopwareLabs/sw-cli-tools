<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\Components\SearchBundleDBAL\Condition;

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
