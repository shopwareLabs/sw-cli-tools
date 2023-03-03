<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Command;

use Shopware\DataGenerator\DataGenerator;
use Shopware\DataGenerator\Struct\Config;
use ShopwareCli\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDataCommand extends BaseCommand
{
    protected $utilities;

    protected $zipDir;

    public function interact(InputInterface $input, OutputInterface $output): void
    {
        $articles = $input->getOption('articles');
        $orders = $input->getOption('orders');
        $customers = $input->getOption('customers');
        $newsletter = $input->getOption('newsletter');
        $categories = $input->getOption('categories');
        $vouchers = $input->getOption('vouchers');

        if ($articles || $orders || $customers || $newsletter || $categories || $vouchers) {
            return;
        }

        $ioService = $this->container->get('io_service');
        $ioService->writeln('<info>Please provide the desired number of…</info>');

        $this->askConfigOptions($input, 'articles', 'Articles', 10000);
        $this->askConfigOptions($input, 'categories', 'Categories', 500);
        $this->askConfigOptions($input, 'categoriesPerArticle', 'Categories per article', 3);
        $this->askConfigOptions($input, 'articleMinVariants', 'Minimum variants per article', 1);
        $this->askConfigOptions($input, 'articleMaxVariants', 'Maximum variants per article', 20);
        $this->askConfigOptions($input, 'articleFilterGroups', 'Filter groups');
        $this->askConfigOptions($input, 'articleFilterOptions', 'Filter Options');
        $this->askConfigOptions($input, 'articleFilterValues', 'Filter Values');
        $this->askConfigOptions($input, 'orders', 'Orders');
        $this->askConfigOptions($input, 'newsletter', 'Newsletters');
        $this->askConfigOptions($input, 'customers', 'Customers', 1000);
        $this->askConfigOptions($input, 'vouchers', 'Vouchers');
    }

    /**
     * @throws \RuntimeException
     */
    public function validateInt($input): int
    {
        if (empty($input)) {
            return 0;
        }
        if (!\is_numeric($input)) {
            throw new \RuntimeException('Field has to be numeric');
        }

        return (int) $input;
    }

    protected function configure(): void
    {
        $this
            ->setName('generate')
            ->addOption(
                'articles',
                'a',
                InputOption::VALUE_OPTIONAL,
                'Number of articles to create',
                0
            )
            ->addOption(
                'articleFilterGroups',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number article filter option groups to create',
                0
            )
            ->addOption(
                'articleFilterOptions',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number article filter options to create',
                0
            )
            ->addOption(
                'articleFilterValues',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number article filter values to create per each filter option',
                0
            )
            ->addOption(
                'articleMinVariants',
                null,
                InputOption::VALUE_OPTIONAL,
                'minimum number of variants',
                0
            )
            ->addOption(
                'articleMaxVariants',
                null,
                InputOption::VALUE_OPTIONAL,
                'max number of variants',
                0
            )
            ->addOption(
                'orders',
                'o',
                InputOption::VALUE_OPTIONAL,
                'Number of orders to create',
                0
            )
            ->addOption(
                'categories',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Number of categories to create',
                0
            )
            ->addOption(
                'categoriesPerArticle',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of categories to assign to each article',
                0
            )
            ->addOption(
                'newsletter',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Number of newsletter to create',
                0
            )
            ->addOption(
                'customers',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Number of customers to create',
                0
            )
            ->addOption(
                'vouchers',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of vouchers to create',
                0
            )
            ->addOption(
                'chunk-size',
                null,
                InputOption::VALUE_OPTIONAL,
                'Chunk size',
                1000
            )
            ->addOption(
                'seed',
                's',
                InputOption::VALUE_OPTIONAL,
                'Random seed',
                0
            )
            ->addOption(
                'installDir',
                null,
                InputOption::VALUE_OPTIONAL,
                'Your Shopware installation path. If provided, data will be automatically injected into the configured database.',
                ''
            )
            ->setDescription('Creates fake data for your Shopware installation.

If executed from a Shopware installation, or if \'installDir\' is provided, the data will be automatically written into your Shopware database
Otherwise, data will be exported to individual files in the \'<current dir>\\output\' folder
You need to import wht .sql files in the order in which they are generated (see the command output)

See the command options to know which data types can be generated
Requires \'local-infile=1\' in your MySQL installation.

<comment>WARNING:</comment>
<comment>This command is DESTRUCTIVE, and will replace existing data in your Shopware database. Do not run in production environments</comment>
            ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var DataGenerator $generator */
        $generator = $this->container->get('data_generator');

        $articles = $input->getOption('articles');
        $orders = $input->getOption('orders');
        $customers = $input->getOption('customers');
        $newsletter = $input->getOption('newsletter');
        $categories = $input->getOption('categories');
        $categoriesPerArticle = $input->getOption('categoriesPerArticle');
        $vouchers = $input->getOption('vouchers');
        $articleFilterGroups = $input->getOption('articleFilterGroups');
        $articleFilterOptions = $input->getOption('articleFilterOptions');
        $articleFilterValues = $input->getOption('articleFilterValues');
        $articleMinVariants = \max($input->getOption('articleMinVariants'), 1);
        $articleMaxVariants = \max($input->getOption('articleMaxVariants'), $articleMinVariants);

        $seed = $input->getOption('seed');
        $chunkSize = $input->getOption('chunk-size');

        if (!($articles || $orders || $customers || $newsletter || $categories || $vouchers)) {
            $output->writeln(
                '<error>At least one of the optional arguments should be provided. Use the --help option for more info.</error>'
            );

            return Command::FAILURE;
        }

        $shopwarePath = $input->getOption('installDir');
        if (!$shopwarePath) {
            $shopwarePath = \realpath(\getcwd());
        }
        $writerManager = $this->container->get('writer_manager');
        if (\is_readable($shopwarePath . '/config.php')) {
            $shopwareConfig = require $shopwarePath . '/config.php';
            if (!\array_key_exists('db', $shopwareConfig)) {
                $output->writeln('<error>Invalid Shopware configuration file.</error>');

                return Command::FAILURE;
            }
            $writerManager->setConfig('database', $shopwareConfig['db']);
            $writerManager->setDefaultWriterType('database');
        } else {
            $writerManager->setDefaultWriterType('file');
        }

        $config = $this->container->get('config');
        $generatorLocale = empty($config['DataGenerator']['locale']) ? null : $config['DataGenerator']['locale'];

        $this->configureGenerator(
            $seed,
            $articles,
            $orders,
            $categories,
            $categoriesPerArticle,
            $customers,
            $newsletter,
            $vouchers,
            $articleFilterGroups,
            $articleFilterOptions,
            $articleFilterValues,
            $chunkSize,
            $articleMinVariants,
            $articleMaxVariants,
            $generatorLocale
        );

        foreach (['categories', 'articles', 'customers', 'orders', 'newsletter', 'vouchers'] as $type) {
            if ($$type > 0) {
                $output->writeln("Creating {$type}");
                $generator->setType($type);
                $generator->run();
            }
        }

        return Command::SUCCESS;
    }

    protected function configureGenerator(
        $seed,
        $articles,
        $orders,
        $categories,
        $categoriesPerArticle,
        $customers,
        $newsletter,
        $vouchers,
        $articleFilterGroups,
        $articleFilterOptions,
        $articleFilterValues,
        $chunkSize,
        $minVariants,
        $maxVariants,
        $generatorLocale
    ): void {
        // Check some pre-conditions
        if ($articles > 0 && !$categories) {
            throw new \UnexpectedValueException('Articles require categories');
        }
        if ($orders > 0 && !$articles) {
            throw new \UnexpectedValueException('Orders require articles');
        }

        /** @var Config $config */
        $config = $this->container->get('generator_config');
        $config->setChunkSize($chunkSize);
        $config->setNumberArticles($articles);
        $config->setNumberCategories($categories);
        $config->setNumberCategoriesPerArticle($categoriesPerArticle);
        $config->setNumberOrders($orders);
        $config->setNumberCustomers($customers);
        $config->setNumberNewsletter($newsletter);
        $config->setNumberVouchers($vouchers);
        $config->setArticleFilterGroups($articleFilterGroups);
        $config->setArticleFilterOptions($articleFilterOptions);
        $config->setArticleFilterValues($articleFilterValues);
        $config->setSeed($seed);
        $config->setGeneratorLocale($generatorLocale);

        $config->setOutputName('');

        // todo: set shopware image dir:
        // $generator->setImageDir()
        $config->setCreateImages(false);
        $config->setThumbnailSizes('105x105,140x140,285x255,30x30,57x57,720x600');

        $config->setMinVariants($minVariants);
        $config->setMaxVariants($maxVariants);
    }

    private function askConfigOptions(InputInterface $input, string $optionName, ?string $optionHumanName = null, int $default = 0): void
    {
        $ioService = $this->container->get('io_service');

        $optionHumanName = $optionHumanName ?: $optionName;
        $optionValue = (int) $input->getOption($optionName);
        if ($optionValue !== 0) {
            return;
        }

        $optionValue = $ioService->askAndValidate(
            "<question>{$optionHumanName}</question> [{$default}]: ",
            [$this, 'validateInt']
        );
        $input->setOption($optionName, \trim($optionValue) ? $optionValue : $default);
    }
}
