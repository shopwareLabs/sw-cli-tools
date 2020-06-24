<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Services;

/**
 * Knows everything about our tables to create proper LOAD DATA INFILE queries
 */
class LoadDataInfile
{
    protected $mappings = [
        's_articles' => [
            'id',
            'supplierID',
            'name',
            'description',
            'description_long',
            'shippingtime',
            'datum',
            'active',
            'taxID',
            'pseudosales',
            'topseller',
            'keywords',
            'changetime',
            'pricegroupID',
            'pricegroupActive',
            'filtergroupID',
            'laststock',
            'crossbundlelook',
            'notification',
            'template',
            'mode',
            'main_detail_id',
            'available_from',
            'available_to',
            'configurator_set_id',
        ],
        's_articles_details' => [
            'id',
            'articleID',
            'ordernumber',
            'suppliernumber',
            'kind',
            'additionaltext',
            'sales',
            'active',
            'instock',
            'stockmin',
            'weight',
            'position',
            'width',
            'height',
            'length',
            'ean',
            'unitID',
            'purchasesteps',
            'maxpurchase',
            'minpurchase',
            'purchaseunit',
            'referenceunit',
            'packunit',
            'releasedate',
            'shippingfree',
            'shippingtime',
        ],
        's_articles_prices' => [
            'pricegroup',
            'from',
            'to',
            'articleID',
            'articledetailsID',
            'price',
            'pseudoprice',
            'baseprice',
            'percent',
        ],
        's_articles_categories' => ['articleID', 'categoryID'],
        's_media' => ['id', 'albumID', 'name', 'path', 'type', 'extension', 'created'],
        's_articles_img' => ['articleID', 'img', 'main', 'extension', 'media_id'],
        's_articles_attributes' => ['id', 'articledetailsID'],
        's_article_configurator_sets' => ['id', 'name'],
        's_article_configurator_option_relations' => ['article_id', 'option_id'],
        's_article_configurator_groups' => ['id', 'name', 'description', 'position'],
        's_article_configurator_options' => ['id', 'group_id', 'name', 'position'],
        's_article_configurator_set_group_relations' => ['set_id', 'group_id'],
        's_article_configurator_set_option_relations' => ['set_id', 'option_id'],
        's_filter_articles' => ['articleID', 'valueID'],
        's_filter' => ['id', 'name', 'position', 'comparable', 'sortmode'],
        's_filter_options' => ['id', 'name', 'filterable'],
        's_filter_values' => ['id', 'optionID', 'value', 'position'],
        's_filter_relations' => ['groupID', 'optionID', 'position'],
        's_articles_categories_ro' => ['articleID', 'categoryID', 'parentCategoryID'],
        's_user' => [
            'id',
            'customernumber',
            'password',
            'email',
            'active',
            'accountmode',
            'confirmationkey',
            'paymentID',
            'firstlogin',
            'lastlogin',
            'sessionID',
            'newsletter',
            'validation',
            'affiliate',
            'customergroup',
            'paymentpreset',
            'language',
            'subshopID',
            'referer',
            'pricegroupID',
            'internalcomment',
            'failedlogins',
            'lockeduntil',
            'default_billing_address_id',
            'default_shipping_address_id',
            'salutation',
            'firstname',
            'lastname',
            'birthday',
        ],
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
        's_user_attributes' => ['userID'],
        's_user_billingaddress' => [
            'id',
            'userID',
            'company',
            'department',
            'salutation',
            'firstname',
            'lastname',
            'street',
            'zipcode',
            'city',
            'phone',
            'countryID',
            'stateID',
            'ustid',
        ],
        's_user_billingaddress_attributes' => ['billingID'],
        's_user_shippingaddress' => [
            'id',
            'userID',
            'company',
            'department',
            'salutation',
            'firstname',
            'lastname',
            'street',
            'zipcode',
            'city',
            'countryID',
            'stateID',
        ],
        's_user_shippingaddress_attributes' => ['shippingID'],
        's_emarketing_vouchers' => [
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
            'taxconfig',
        ],
        's_emarketing_vouchers_attributes' => ['voucherID'],
        's_emarketing_voucher_codes' => ['voucherID', 'userID', 'code', 'cashed'],
    ];

    /**
     * Returns a loadDataInfile query for the selected table / file
     */
    public function get($tableName, $file): string
    {
        if (!$columns = $this->mappings[$tableName]) {
            throw new \RuntimeException("No definition for $tableName");
        }

        $columns = implode(
            ', ',
            array_map(
                static function ($column) {
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
