<?php

namespace ShopwareCli\Plugin\ShopwareInstall\Services;

use ShopwareCli\Utilities;
use ShopwareCli\Application\Logger;

/**
 * The checkout service will checkout a given repo with a given branch to a given destination
 *
 * Class Checkout
 * @package ShopwareCli\Plugin\ShopwareInstall\Services
 */
class Checkout
{
    protected $utilities;
    protected $logger;

    public function __construct(Utilities $utilities, Logger $logger)
    {
        $this->utilities = $utilities;
        $this->logger = $logger;
    }

    public function checkout($repo, $branch, $destination)
    {
        Logger::info("<info>Checkout out $repo to $destination</info>");

        $this->utilities->executeCommand(
            "git clone -b {$branch} {$repo} {$destination}"
        );
    }

}
