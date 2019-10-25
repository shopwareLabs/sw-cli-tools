<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Resources;

use Shopware\DataGenerator\Services\LoadDataInfile;
use Shopware\DataGenerator\Writer\WriterInterface;

class Vouchers extends BaseResource
{
    /**
     * @var int
     */
    protected $numberOfIndividualVoucherCodes = 1000;

    /**
     * @var array
     */
    protected $tables = [
        's_emarketing_vouchers',
        's_emarketing_vouchers_attributes',
        's_emarketing_vouchers_cashed',
        's_emarketing_voucher_codes',
    ];

    /**
     * @var LoadDataInfile
     */
    private $loadDataInfile;

    /**
     * {@inheritdoc}
     */
    public function create(WriterInterface $writer)
    {
        $number = $this->config->getNumberVouchers();
        $this->loadDataInfile = new LoadDataInfile();

        $voucherCsv = $this->writerManager->createWriter('vouchers', 'csv');
        $voucherCodeCsv = $this->writerManager->createWriter('voucher_code', 'csv');
        $voucherAttributeCsv = $this->writerManager->createWriter('voucher_code', 'csv');

        $this->createProgressBar($number);

        for ($voucherCounter = 0; $voucherCounter < $number; ++$voucherCounter) {
            $this->advanceProgressBar();

            $id = $this->getUniqueId('voucher');

            $isIndividual = $voucherCounter % 2 === 1;
            $isPercental = !$voucherCounter % 3;
            $numOrder = !$isIndividual; // individual vouchers are not restricted

            $code = 'code' . $id;
            if ($isIndividual) {
                $code = '';
            }
            $value = mt_rand(5, 100);

            $voucherCsv->write(
                "{$id},Voucher #{$id},{$code},{$value},101,\N,\N,VOUCHER{$id},{$isIndividual},{$isPercental},{$numOrder},\N,auto"
            );
            $voucherAttributeCsv->write("{$id}");

            if ($isIndividual) {
                for ($i = 0; $i < $this->numberOfIndividualVoucherCodes; ++$i) {
                    $code = "code-{$id}-{$i}";
                    $voucherCodeCsv->write("{$id},\N,{$code},0");
                }
            }
        }

        $writer->write($this->loadDataInfile->get('s_emarketing_vouchers', $voucherCsv->getFileName()));
        $writer->write(
            $this->loadDataInfile->get('s_emarketing_vouchers_attributes', $voucherAttributeCsv->getFileName())
        );
        $writer->write($this->loadDataInfile->get('s_emarketing_voucher_codes', $voucherCodeCsv->getFileName()));
    }
}
