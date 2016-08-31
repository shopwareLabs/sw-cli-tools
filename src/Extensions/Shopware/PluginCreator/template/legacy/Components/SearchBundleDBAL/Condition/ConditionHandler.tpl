<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

<?php if ($configuration->isLegacyPlugin) { ?>
namespace <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\Components\SearchBundleDBAL\Condition;
<?php } else { ?>
namespace <?= $configuration->name; ?>\Components\SearchBundleDBAL\Condition;
<?php } ?>

use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class <?= $configuration->name; ?>ConditionHandler implements ConditionHandlerInterface
{
    const STATE_INCLUDED = '<?= $names->under_score_js; ?>_included';

    /**
     * @inheritdoc
     */
    public function supportsCondition(ConditionInterface $condition)
    {
        return ($condition instanceof <?= $configuration->name; ?>Condition);
    }

    /**
     * @inheritdoc
     */
    public function generateCondition(
        ConditionInterface $condition,
        QueryBuilder $query,
        ShopContextInterface $context
    ) {

        if (!$query->hasState(self::STATE_INCLUDED)) {
            // implement your condition here.

            // e.g. limit to product.ids <= 100
            $query->andWhere('product.id <= 100');

            // e.g. join another table and only include products,
            // that are referenced in that table.
            // $query->innerJoin(
            //     'product',
            //     's_bundle_articles',
            //     'bundle',
            //     'bundle.article_id = product.id'
            // );
            $query->addState(self::STATE_INCLUDED);
        }
    }
}
