<?php

namespace Shopware\DataGenerator\Services;

/**
 * Knows everything about our tables to create proper LOAD DATA INFILE queries
 *
 * Class LoadDataInfile
 * @package Shopware\DataGenerator\Services
 */
class LoadDataInfile
{
    protected $mappings = array(
        's_articles' => array(
            "id",
            "supplierID",
            "name",
            "description",
            "description_long",
            "shippingtime",
            "datum",
            "active",
            "taxID",
            "pseudosales",
            "topseller",
            "keywords",
            "changetime",
            "pricegroupID",
            "pricegroupActive",
            "filtergroupID",
            "laststock",
            "crossbundlelook",
            "notification",
            "template",
            "mode",
            "main_detail_id",
            "available_from",
            "available_to",
            "configurator_set_id"
        ),
        's_articles_details' => array(
            "id",
            "articleID",
            "ordernumber",
            "suppliernumber",
            "kind",
            "additionaltext",
            "sales",
            "active",
            "instock",
            "stockmin",
            "weight",
            "position",
            "width",
            "height",
            "length",
            "ean",
            "unitID",
            "purchasesteps",
            "maxpurchase",
            "minpurchase",
            "purchaseunit",
            "referenceunit",
            "packunit",
            "releasedate",
            "shippingfree",
            "shippingtime"
        ),
        's_articles_prices' => array(
            "pricegroup",
            "from",
            "to",
            "articleID",
            "articledetailsID",
            "price",
            "pseudoprice",
            "baseprice",
            "percent"
        ),
        's_articles_categories' => array("articleID", "categoryID"),
        's_media' => array("id", "albumID", "name", "path", "type", "extension", "created"),
        's_articles_img' => array("articleID", "img", "main", "extension", "media_id"),
        's_articles_attributes' => array("articleID", "articledetailsID"),
        's_article_configurator_sets' => array("id", "name"),
        's_article_configurator_option_relations' => array("article_id", "option_id"),
        's_article_configurator_groups' => array("id", "name", "description", "position"),
        's_article_configurator_options' => array("id", "group_id", "name", "position"),
        's_article_configurator_set_group_relations' => array("set_id", "group_id"),
        's_article_configurator_set_option_relations' => array("set_id", "option_id"),
        's_filter_articles' => array("articleID", "valueID"),
        's_filter' => array("id", "name", "position", "comparable", "sortmode"),
        's_filter_options' => array("id", "name", "filterable"),
        's_filter_values' => array("id", "optionID", "value", "position"),
        's_filter_relations' => array("groupID", "optionID", "position"),
        's_articles_categories_ro' => array("articleID", "categoryID", "parentCategoryID"),
        's_user' => array(
            "id",
            "customernumber",
            "password",
            "email",
            "active",
            "accountmode",
            "confirmationkey",
            "paymentID",
            "firstlogin",
            "lastlogin",
            "sessionID",
            "newsletter",
            "validation",
            "affiliate",
            "customergroup",
            "paymentpreset",
            "language",
            "subshopID",
            "referer",
            "pricegroupID",
            "internalcomment",
            "failedlogins",
            "lockeduntil",
            "default_billing_address_id",
            "default_shipping_address_id",
        ),
        's_user_addresses' => [
            'id',
            'user_id',
            'company',
            'department',
            'salutation',
            'firstname',
            'lastname',
            'street',
            'zipcode',
            'city',
            'country_id',
            'state_id',
            'ustid',
            'phone',
            'additional_address_line1',
            'additional_address_line2',
            'title',
        ],
        's_user_attributes' => array("userID"),
        's_user_billingaddress' => array(
            "id",
            "userID",
            "company",
            "department",
            "salutation",
            "firstname",
            "lastname",
            "street",
            "zipcode",
            "city",
            "phone",
            "countryID",
            "stateID",
            "ustid"
        ),
        's_user_billingaddress_attributes' => array("billingID"),
        's_user_shippingaddress' => array(
            "id",
            "userID",
            "company",
            "department",
            "salutation",
            "firstname",
            "lastname",
            "street",
            "zipcode",
            "city",
            "countryID",
            "stateID"
        ),
        's_user_shippingaddress_attributes' => array("shippingID"),
        's_emarketing_vouchers' => array(
            'id',
            'description',
            'vouchercode',
            'value',
            'minimumcharge',
            'valid_from',
            'valid_to',
            'ordercode',
            'modus',
            'percental',
            'numorder',
            'customergroup',
            'taxconfig'
        ),
        's_emarketing_vouchers_attributes' => array('voucherID'),
        's_emarketing_voucher_codes' => array('voucherID', 'userID', 'code', 'cashed')
    );


    /**
     * Returns a loadDataInfile query for the selected table / file
     *
     * @param $tableName
     * @param $file
     * @return string
     */
    public function get($tableName, $file)
    {
        if (!$columns = $this->mappings[$tableName]) {
            throw new \RuntimeException("No definition for $tableName");
        }

        $columns = implode(
            ", ",
            array_map(
                function ($column) {
                    return "`$column`";
                },
                $columns
            )
        );

        return <<<EOD
LOAD DATA LOCAL INFILE '{$file}' IGNORE INTO TABLE {$tableName}
FIELDS TERMINATED BY ',' LINES TERMINATED BY '\\n'
({$columns});
EOD;
    }
}
