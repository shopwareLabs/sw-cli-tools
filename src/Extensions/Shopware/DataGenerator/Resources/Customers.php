<?php

namespace Shopware\DataGenerator\Resources;

use Faker\Factory;
use Shopware\DataGenerator\Services\LoadDataInfile;
use Shopware\DataGenerator\Writer\WriterInterface;

class Customers extends BaseResource
{
    /**
     * @var array
     */
    protected $tables = [
        's_user',
        's_user_attributes',
        's_user_addresses',
        's_user_billingaddress',
        's_user_billingaddress_attributes',
        's_user_shippingaddress',
        's_user_shippingaddress_attributes'
    ];

    /**
     * Stores the used ids for SQL inserts
     * @var array
     */
    protected $ids = [];

    /**
     * @var LoadDataInfile
     */
    private $loadDataInfile;

    /**
     * @inheritdoc
     */
    public function create(WriterInterface $writer)
    {
        $this->ids['customerNumber'] = 20002;
        $number = $this->config->getNumberCustomers();
        $this->loadDataInfile = new LoadDataInfile();

        $importCustomers = $this->writerManager->createWriter('customers', 'csv');
        $importCustomersAttributes = $this->writerManager->createWriter('customers_attributes', 'csv');
        $importCustomersAddresses = $this->writerManager->createWriter('customers_addresses', 'csv');
        $importCustomersBilling = $this->writerManager->createWriter('customers_billing', 'csv');
        $importCustomersBillingAttributes = $this->writerManager->createWriter('customers_billing_attributes', 'csv');
        $importCustomersShipping = $this->writerManager->createWriter('customers_shipping', 'csv');
        $importCustomersShippingAttributes = $this->writerManager->createWriter('customers_shipping_attributes', 'csv');


        $this->createProgressBar($number);

        for ($customerCounter = 0; $customerCounter < $number; $customerCounter++) {
            $faker = Factory::create();

            $this->advanceProgressBar();

            $id = $this->getUniqueId('customer');
            $customerNumber = $this->getUniqueId('customerNumber');

            $group = $customerCounter % 2 === 1 ? 'EK' : 'H';

            $sex = $customerCounter % 2 === 1 ? 'mr' : 'ms';

            $birthday = '';

            if (rand(1, 4) !== 4) {
                $birthday = $faker->dateTimeThisCentury->format('Y-m-d');
            }

            $importCustomers->write(
                "{$id},{$customerNumber},a256a310bc1e5db755fd392c524028a8,{$faker->email},1,0,,5,2013-01-11,2015-01-01 00:00:00,,0,,0,{$group},0,1,1,,\N,,0,\N,{$id},{$id},{$sex},{$faker->firstName},{$faker->lastName}, {$birthday}"
            );
            $importCustomersAttributes->write("{$id}");
            $importCustomersBillingAttributes->write("{$id}");
            $importCustomersBillingAttributes->write("{$id}");

            $customerAttributeValues[] = "{$id}";
            $importCustomersBilling->write(
                "{$id}, {$id}, , , {$sex}, {$this->generator->getRandomFirstName()}, {$this->generator->getRandomLastName()}, {$this->generator->getRandomWord()} " . rand(
                    1,
                    500
                ) . "' , " . rand(42000, 50000) . ", {$this->generator->getRandomCity()}, " . rand(
                    9999,
                    99999
                ) . ', 2, 0, '
            );
            $importCustomersShipping->write(
                "{$id}, {$id}, , , {$sex}, {$this->generator->getRandomFirstName()}, {$this->generator->getRandomLastName()}, {$this->generator->getRandomWord()} " . rand(
                    1,
                    500
                ) . "' , " . rand(42000, 50000) . ", {$this->generator->getRandomCity()}, 2, 0"
            );

            $zip = rand(42000, 50000);
            $importCustomersAddresses->write(
                "{$id}, {$id}, company, department, mr, {$this->generator->getRandomFirstName()}, {$this->generator->getRandomLastName()}, {$this->generator->getRandomWord()}, {$zip}, {$this->generator->getRandomCity()}, 2, 0, ustid, phone,,,"
            );
        }

        $writer->write($this->loadDataInfile->get('s_user', $importCustomers->getFileName()));
        $writer->write(
            $this->loadDataInfile->get('s_user_attributes', $importCustomersAttributes->getFileName())
        );
        $writer->write(
            $this->loadDataInfile->get('s_user_addresses', $importCustomersAddresses->getFileName())
        );
        $writer->write(
            $this->loadDataInfile->get('s_user_billingaddress', $importCustomersBilling->getFileName())
        );
        $writer->write(
            $this->loadDataInfile->get('s_user_shippingaddress', $importCustomersShipping->getFileName())
        );
        $writer->write(
            $this->loadDataInfile->get(
                's_user_billingaddress_attributes',
                $importCustomersBillingAttributes->getFileName()
            )
        );
        $writer->write(
            $this->loadDataInfile->get(
                's_user_shippingaddress_attributes',
                $importCustomersShippingAttributes->getFileName()
            )
        );

        $writer->write('UPDATE s_user SET birthday = NULL WHERE birthday = "0000-00-00 00:00"');
    }
}
