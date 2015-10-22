<?php

namespace Shopware\DataGenerator\Resources;

use Shopware\DataGenerator\Services\LoadDataInfile;
use Shopware\DataGenerator\Writer\WriterInterface;

class Customers extends BaseResource
{
    /**
     * @var array
     */
    protected $tables = array(
        "s_user",
        "s_user_attributes",
        "s_user_billingaddress",
        "s_user_billingaddress_attributes",
        "s_user_debit",
        "s_user_shippingaddress",
        "s_user_shippingaddress_attributes"
    );

    /**
     * Stores the used ids for SQL inserts
     * @var array
     */
    protected $ids = array();

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
        $importCustomersBilling = $this->writerManager->createWriter('customers_billing', 'csv');
        $importCustomersBillingAttributes = $this->writerManager->createWriter(
            'customers_billing_attributes',
            'csv'
        );


        $this->createProgressBar($number);

        for ($customerCounter = 0; $customerCounter < $number; $customerCounter++) {
            $this->advanceProgressBar();

            $id = $this->getUniqueId('customer');
            $customerNumber = $this->getUniqueId('customerNumber');

            $group = $customerCounter % 2 === 1 ? "EK" : "H";
            $sex = $customerCounter % 2 === 1 ? "mr" : "ms";

            $importCustomers->write(
                "{$id},a256a310bc1e5db755fd392c524028a8,user_{$id}@example.org,1,0,,5,2013-01-11,2015-01-01 00:00:00,,0,,0,{$group},0,1,1,,\N,,0,\N"
            );
            $importCustomersAttributes->write("{$id}");
            $importCustomersBillingAttributes->write("{$id}");
            $importCustomersBillingAttributes->write("{$id}");

            $customerAttributeValues[] = "{$id}";
            $importCustomersBilling->write(
                "{$id}, {$id}, , , {$sex}, {$customerNumber}, {$this->generator->getRandomFirstName()}, {$this->generator->getRandomLastName()}, {$this->generator->getRandomWord()} ".rand(
                    1,
                    500
                )."' , ".rand(42000, 50000).", {$this->generator->getRandomCity()}, ".rand(
                    9999,
                    99999
                ).", , 2, 0, , 1992-04-03"
            );
        }

        $writer->write($this->loadDataInfile->get('s_user', $importCustomers->getFileName()));
        $writer->write(
            $this->loadDataInfile->get('s_user_attributes', $importCustomersAttributes->getFileName())
        );
        $writer->write(
            $this->loadDataInfile->get('s_user_billingaddress', $importCustomersBilling->getFileName())
        );
        $writer->write(
            $this->loadDataInfile->get(
                's_user_billingaddress_attributes',
                $importCustomersBillingAttributes->getFileName()
            )
        );
    }
}
