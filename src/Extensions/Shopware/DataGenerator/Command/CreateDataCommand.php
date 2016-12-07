<?php

namespace Shopware\DataGenerator\Command;

use Shopware\DataGenerator\DataGenerator;
use Shopware\DataGenerator\Struct\Config;
use ShopwareCli\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDataCommand extends BaseCommand
{
    protected $utilities;
    protected $zipDir;

    protected function configure()
    {
        $this
            ->setName('generate')
            ->addOption(
                'articles',
                'a',
                InputOption::VALUE_OPTIONAL,
                'Number of articles to create',
                null
            )
            ->addOption(
                'articleFilterGroups',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number article filter option groups to create',
                null
            )
            ->addOption(
                'articleFilterOptions',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number article filter options to create',
                null
            )
            ->addOption(
                'articleFilterValues',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number article filter values to create per each filter option',
                null
            )
            ->addOption(
                'orders',
                'o',
                InputOption::VALUE_OPTIONAL,
                'Number of orders to create',
                null
            )
            ->addOption(
                'categories',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Number of categories to create',
                null
            )
            ->addOption(
                'categoriesPerArticle',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of categories to assign to each article',
                null
            )
            ->addOption(
                'newsletter',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Number of newsletter to create',
                null
            )
            ->addOption(
                'customers',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Number of customers to create',
                null
            )
            ->addOption(
                'vouchers',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of vouchers to create',
                null
            )
            ->addOption(
                'chunk-size',
                null,
                InputOption::VALUE_OPTIONAL,
                'Chung size',
                null
            )
            ->addOption(
                'seed',
                's',
                InputOption::VALUE_OPTIONAL,
                'Random seed',
                null
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function interact(InputInterface $input, OutputInterface $output)
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

        $this->askConfigOptions($input, 'articles');
        $this->askConfigOptions($input, 'categories');
        $this->askConfigOptions($input, 'categoriesPerArticle', 'categories per article');
        $this->askConfigOptions($input, 'orders');
        $this->askConfigOptions($input, 'newsletter');
        $this->askConfigOptions($input, 'customers');
        $this->askConfigOptions($input, 'vouchers');
    }

    /**
     * @param InputInterface $input
     * @param string optionName
     * @param string|null $optionHumanName
     */
    private function askConfigOptions(InputInterface $input, $optionName, $optionHumanName = null)
    {
        $ioService = $this->container->get('io_service');

        $optionHumanName = $optionHumanName ? : $optionName;
        $optionValue = $input->getOption($optionName);
        if ($optionValue != 0) {
            return;
        }

        $optionValue = $ioService->askAndValidate(
            "Please provide the value for {$optionHumanName}, use 0 or leave empty to skip: ",
            [$this, 'validateInt']
        );
        $input->setOption($optionName, trim($optionValue) ? $optionValue : null);
    }

    /**
     * @param  string
     * @return int
     * @throws \RuntimeException
     */
    public function validateInt($input)
    {
        if (empty($input)) {
            return 0;
        }
        if (!is_numeric($input)) {
            throw new \RuntimeException("Field has to be numeric");
        }

        return intval($input);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
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
        $seed = $input->getOption('seed');
        $chunkSize = $input->getOption('chunk-size') ?: 1000;

        if (!($articles || $orders || $customers || $newsletter || $categories || $vouchers)) {
            $output->writeln(
                '<error>At least one of the optional arguments should be provided. Use the --help option for more info.</error>'
            );

            return;
        }

        $shopwarePath = $input->getOption('installDir');
        if (!$shopwarePath) {
            $shopwarePath = realpath(getcwd());
        }
        $writerManager = $this->container->get('writer_manager');
        if (is_readable($shopwarePath.'/config.php')) {
            $shopwareConfig = require $shopwarePath.'/config.php';
            if (!array_key_exists('db', $shopwareConfig)) {
                $output->writeln('<error>Invalid Shopware configuration file.</error>');

                return;
            }
            $writerManager->setConfig('database', $shopwareConfig['db']);
            $writerManager->setDefaultWriterType('database');
        } else {
            $writerManager->setDefaultWriterType('file');
        }

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
            $chunkSize
        );

        foreach (['categories', 'articles', 'customers', 'orders', 'newsletter', 'vouchers'] as $type) {
            if ($$type > 0) {
                $output->writeln("Creating {$type}");
                $generator->setType($type);
                $generator->run();
            }
        }
    }

    /**
     * @param $seed
     * @param $articles
     * @param $orders
     * @param $categories
     * @param $categoriesPerArticle
     * @param $customers
     * @param $newsletter
     * @param $vouchers
     * @param $articleFilterGroups
     * @param $articleFilterOptions
     * @param $articleFilterValues
     * @param $chunkSize
     */
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
        $chunkSize
    ) {
        // Check some pre-conditions
        if ($articles > 0 && !$categories) {
            throw new \UnexpectedValueException("Articles require categories");
        }
        if ($orders > 0 && !$articles) {
            throw new \UnexpectedValueException("Orders require articles");
        }

        /** @var Config $config */
        $config = $this->container->get('generator_config');
        if ($chunkSize !== null) {
            $config->setChunkSize($chunkSize);
        }
        if ($articles !== null) {
            $config->setNumberArticles($articles);
        }
        if ($categories !== null) {
            $config->setNumberCategories($categories);
        }
        if ($categoriesPerArticle !== null) {
            $config->setNumberCategoriesPerArticle($categoriesPerArticle);
        }
        if ($orders !== null) {
            $config->setNumberOrders($orders);
        }
        if ($customers !== null) {
            $config->setNumberCustomers($customers);
        }
        if ($newsletter !== null) {
            $config->setNumberNewsletter($newsletter);
        }
        if ($vouchers !== null) {
            $config->setNumberVouchers($vouchers);
        }
        if ($articleFilterGroups !== null) {
            $config->setArticleFilterGroups($articleFilterGroups);
        }
        if ($articleFilterOptions !== null) {
            $config->setArticleFilterOptions($articleFilterOptions);
        }
        if ($articleFilterValues !== null) {
            $config->setArticleFilterValues($articleFilterValues);
        }
        if ($seed !== null) {
            $config->setSeed($seed);
        }

        $config->setOutputName('');

        //todo: set shopware image dir:
        // $generator->setImageDir()
        $config->setCreateImages(false);
        $config->setThumbnailSizes('105x105,140x140,285x255,30x30,57x57,720x600');

        $config->setMinVariants(3);
        $config->setMaxVariants(50);
    }
}
