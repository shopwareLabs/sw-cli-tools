<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\ESIndexingBundle;

use Doctrine\DBAL\Connection;

class Provider
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int[] $ids
     * @return []
     */
    public function get($ids)
    {
    }
}
