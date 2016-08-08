<?php

namespace Shopware\DataGenerator\Resources;

use Faker\Factory;
use Shopware\DataGenerator\Writer\WriterInterface;

class Orders extends BaseResource
{
    const DEVICES = [
        'desktop',
        'mobile',
        'tablet'
    ];

    /**
     * @var array
     */
    protected $tables = [
        's_order',
        's_order_details',
        's_order_billingaddress',
        's_order_billingaddress_attributes',
        's_order_shippingaddress',
        's_order_shippingaddress_attributes'
    ];

    /**
     * Number of article details available
     */
    protected $numberDetails;

    /**
     * @var Articles
     */
    private $articleResource;

    /**
     * @param Articles $articleResource
     */
    public function setArticleResource(Articles $articleResource)
    {
        $this->articleResource = $articleResource;
    }

    /**
     * @inheritdoc
     */
    public function create(WriterInterface $writer)
    {
        $orderCSVWriter = $this->writerManager->createWriter('order', 'csv');

        $number = $this->config->getNumberOrders();
        $this->createProgressBar($number);

        if (!$this->articleResource) {
            throw new \RuntimeException('Article resource not available');
        }

        // Force order numbers to start at 10001
        $this->ids['ordernumber'] = 1000;


        $valueData = [
            'orderValues' => [],
            'orderDetailValues' => [],
            'customerBillingValues' => [],
            'customerShippingValues' => [],
            'customerBillingAttributeValues' => [],
        ];

        $articleDetails = $this->articleResource->getArticleDetailsFlat();

        $totalNumberCustomers = $this->config->getNumberCustomers();
        $orderNumbers = [];
        $orderDates = [];

        for ($orderCounter = 0; $orderCounter < $number; $orderCounter++) {
            $faker = Factory::create();

            $id = $this->getUniqueId('order');
            $orderNumber = $this->getUniqueId('ordernumber');

            $orderNumbers[] = $orderNumber;

            $currentCustomer = rand(1, $totalNumberCustomers);
            $currentCustomerNumber = 20002 + $currentCustomer;
            // Create a large order for the first order
            $numArticles = $id === 1 ? 100 : rand(1, 4);
            $totalPrice = $numArticles * 47.90;
            $totalPricePreTax = $totalPrice / 1.19;

            // Create faster inserts by using dummy data instead of INSERT..SELECTING the data from s_user_billingaddress/shippingaddress
            $randomFirstName = $this->escapeApostrophes($this->generator->getRandomFirstName());
            $randomLastName = $this->escapeApostrophes($this->generator->getRandomLastName());
            $randomStreet = $this->escapeApostrophes($this->generator->getRandomWord() . rand(1,500));
            $randomZip = rand(42000, 50000);
            $randomCity = $this->escapeApostrophes($this->generator->getRandomCity());
            $valueData['customerBillingValues'][] = "( {$currentCustomer}, {$id}, '', '', 'mr', $currentCustomerNumber, '{$this->quote($faker->firstName)}', '{$this->quote($faker->lastName)}', '{$this->quote($faker->streetAddress)}', '{$this->quote($faker->postcode)}', '{$this->quote($faker->city)}', '', 2, 0 )";
            $valueData['customerShippingValues'][] = "( {$currentCustomer}, {$id}, '', '', 'mr', '{$this->quote($faker->firstName)}', '{$this->quote($faker->lastName)}', '{$this->quote($faker->streetAddress)}', '{$this->quote($faker->postcode)}', '{$this->quote($faker->city)}', 2, 0)";
            $valueData['customerBillingAttributeValues'][] = "({$id}, {$id})";

            $cleared = rand(9, 21);
            $state = rand(0, 8);
            $payment = rand(2, 6);
            $device = self::DEVICES[rand(0, count(self::DEVICES) - 1)];
            $date = $faker->dateTimeBetween('-2years', 'now');

            $dateFormatted = $date->format('Y-m-d');

            if (isset($orderDates[$dateFormatted])) {
                $orderDates[$dateFormatted]++;
            } else {
                $orderDates[$dateFormatted] = 0;
            }


            $valueData['orderValues'][] = "({$id}, $orderNumber, {$currentCustomer}, {$totalPrice}, {$totalPricePreTax}, 0, 0, '{$date->format('Y-m-d H:i:s')}', {$state}, {$cleared}, {$payment}, '', '', '', '', 1, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '217.86.205.141', '{$device}')";

            for ($detailCounter = 1; $detailCounter <= $numArticles; $detailCounter++) {
                $detailId = $this->getUniqueId('orderDetail');

                $articleId = $articleDetails[rand(1, $this->articleResource->getIds('article'))];
                $valueData['orderDetailValues'][] = "({$detailId}, {$id}, '{$orderNumber}', '{$articleId}', 'sw-not-real', 47.90, 1, '{$this->generator->getSentence(3)}', 1, 0, 0)";
            }
        }

        $begin = new \DateTime(min(array_keys($orderDates)));
        $end = new \DateTime(max(array_keys($orderDates)));

        $interval = \DateInterval::createFromDateString('1 day');

        foreach (new \DatePeriod($begin, $interval, $end) as $dt) {
            $date = $dt->format('Y-m-d');

            $ordersOnDate = isset($orderDates[$date]) ? $orderDates[$date] : 10;
            $pageImpressions = $ordersOnDate * rand(200, 1500);
            $uniqueVisits = $ordersOnDate * rand(1, 50);

            $valueData['visitors'][] = "(1, \"{$date}\", {$pageImpressions}, {$uniqueVisits}, \"desktop\")";
        }

        $writer->write($this->createSQL($valueData));
        $orderCSVWriter->write($orderNumbers);

        $this->finishProgressBar();
    }

    private function escapeApostrophes($string){
        $string = str_replace("'", "\\'", $string);
        $string = str_replace("`", "\\`", $string);
        return $string;
    }

    /**
     * Constructs the actual inserts from the passed arrays
     * @param $valueData
     * @return string[]
     */
    private function createSQL($valueData)
    {
        $sql = [];

        $sql[] = '
            INSERT INTO `s_order` (`id`,`ordernumber`, `userID`, `invoice_amount`, `invoice_amount_net`, `invoice_shipping`, `invoice_shipping_net`, `ordertime`, `status`, `cleared`, `paymentID`, `transactionID`, `comment`,  `customercomment`, `internalcomment`, `net`, `taxfree`, `partnerID`, `temporaryID`, `referer`, `cleareddate`, `trackingcode`, `language`, `dispatchID`, `currency`, `currencyFactor`, `subshopID`, `remote_addr`, `deviceType`) VALUES '.implode(
                ",\n            ",
                $valueData['orderValues']
            ).';';

        $sql[] = '
            INSERT INTO `s_order_details` (`id`, `orderID`, `ordernumber`, `articleID`, `articleordernumber`, `price`, `quantity`, `name`, `status`, `shipped`, `shippedgroup`) VALUES
            '.implode(',', $valueData['orderDetailValues']).';';

        $sql[] = '
            INSERT INTO `s_order_billingaddress` (`userID`, `orderID`, `company`, `department`, `salutation`, `customernumber`, `firstname`, `lastname`, `street`, `zipcode`, `city`, `phone`, `countryID`, `stateID`) VALUES '.implode(
                ' , ',
                $valueData['customerBillingValues']
            ).';';
        $sql[] = '
            INSERT INTO `s_order_shippingaddress` (`userID`, `orderID`, `company`, `department`, `salutation`, `firstname`, `lastname`, `street`, `zipcode`, `city`, `countryID`, `stateID`) VALUES '.implode(
                ' , ',
                $valueData['customerShippingValues']
            ).';';

        $sql[] = '
            INSERT INTO `s_order_shippingaddress_attributes` (`id`, `shippingID`) VALUES '.implode(
                ', ',
                $valueData['customerBillingAttributeValues']
            ).';';
        $sql[] = '
            INSERT INTO `s_order_billingaddress_attributes` (`id`, `billingID`) VALUES '.implode(
                ', ',
                $valueData['customerBillingAttributeValues']
            ).';';

        $sql[] = '
            INSERT INTO `s_statistics_visitors` (`shopID`, `datum`, `pageimpressions`, `uniquevisits`, `deviceType`) VALUES '.implode(
                ', ',
                $valueData['visitors']
            ).';';


        return $sql;
    }

    /**
     * @param $string
     * @return string
     */
    private function quote($string)
    {
        return str_replace('\'', "\'", $string);
    }
}
