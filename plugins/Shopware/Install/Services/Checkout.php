<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\IoService;
use ShopwareCli\Utilities;

/**
 * The checkout service will checkout a given repo with a given branch to a given destination
 *
 * Class Checkout
 * @package Shopware\Install\Services
 */
class Checkout
{
    protected $utilities;
    /**
     * @var \ShopwareCli\Services\IoService
     */
    private $ioService;

    public function __construct(Utilities $utilities, IoService $ioService)
    {
        $this->utilities = $utilities;
        $this->ioService = $ioService;
    }

    public function checkout($repo, $branch, $destination)
    {
        $this->ioService->writeln("<info>Checkout out $repo to $destination</info>");

        $this->utilities->executeCommand(
            "git clone -b {$branch} {$repo} {$destination}"
        );
    }

}
