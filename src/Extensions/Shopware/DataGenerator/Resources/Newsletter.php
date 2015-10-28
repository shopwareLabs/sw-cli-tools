<?php

namespace Shopware\DataGenerator\Resources;

use Shopware\DataGenerator\Writer\WriterInterface;

class Newsletter extends BaseResource
{
    protected $tables = array(
        "s_campaigns_articles",
        "s_campaigns_banner",
        "s_campaigns_containers",
        "s_campaigns_groups",
        "s_campaigns_html",
        "s_campaigns_links",
        "s_campaigns_logs",
        "s_campaigns_mailaddresses",
        "s_campaigns_maildata",
        "s_campaigns_mailings",
        "s_campaigns_positions",
        "s_campaigns_sender",
        "s_campaigns_templates"
    );

    /**
     * @inheritdoc
     */
    public function create(WriterInterface $writer)
    {
        $number = $this->config->getNumberNewsletter();
        $this->createProgressBar($number);

        $addressValues = array();
        for ($i = 0; $i < $number; $i++) {
            $this->advanceProgressBar();
            $addressId = $this->getUniqueId('addressId');

            $addressValues[] = "({$addressId}, 0, 1, 'newsletter_test_{$addressId}@shopware.de', 0, 0)";
        }

        // Recipients
        $writer->write(
            sprintf(
                "INSERT IGNORE INTO `s_campaigns_mailaddresses` (`id`, `customer`, `groupID`, `email`, `lastmailing`, `lastread`) VALUES %s;",
                implode(",\n ", $addressValues)
            )
        );

        // Newsletter group
        $writer->write(
            "INSERT IGNORE INTO `s_campaigns_groups` (`id`, `name`) VALUES (1, 'Newsletter-Empfänger');"
        );

        // Groups assigned to the newsletter
        $groups = array(
            array("EK" => 0, "H" => 0),
            array(1 => 0)
        );
        $groups = serialize($groups);

        // The actual newsletter
        $writer->write(
            "INSERT IGNORE INTO `s_campaigns_mailings` (`id`, `datum`, `groups`, `subject`, `sendermail`, `sendername`, `plaintext`,
                `templateID`, `languageID`, `status`, `locked`, `recipients`, `read`, `clicked`, `customergroup`, `publish`)
            VALUES (1, '2013-04-25', '{$groups}', 'Newsletter-Test', 'info@example.com',
                'Newsletter Absender', 0, 1, 1, 0, '2013-04-25 17:45:12', {$this->config->getNumberNewsletter()}, 0, 0, 'EK', 1
            );"
        );

        // Container
        $writer->write(
            "INSERT IGNORE INTO `s_campaigns_containers` (`id`, `promotionID`, `type`, `description`, `position`)
            VALUES (1, 1, 'ctText', 'Newsletter-Test', 1);"
        );

        // HTML Text
        $writer->write(
            "INSERT IGNORE INTO `s_campaigns_html` (`id`, `parentID`, `headline`, `html`, `image`, `link`, `alignment`)
            VALUES (1, '1', 'Newsletter-Test', '<p>Hallo <strong>Welt</strong></p>', '', '', 'left');"
        );

        // Sender Mail
        $writer->write(
            "INSERT IGNORE INTO `s_campaigns_sender` (`email`, `name`) VALUES ('info@example.com', 'Newsletter Absender');"
        );

        // Template
        $writer->write(
            "INSERT INTO `s_campaigns_templates` (`id`, `path`, `description`) VALUES
            (1, 'index.tpl', 'Standardtemplate'),
            (2, 'indexh.tpl', 'Händler');"
        );

        $this->finishProgressBar();
    }
}
