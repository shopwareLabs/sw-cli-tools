<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\IoService;
use ShopwareCli\Services\ProcessExecutor;

/**
 * The checkout service will checkout a given repo with a given branch to a given destination
 *
 * Class Checkout
 * @package Shopware\Install\Services
 */
class Checkout
{
    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @var ProcessExecutor
     */
    private $processExecutor;

    /**
     * @param ProcessExecutor $processExecutor
     * @param IoService       $ioService
     */
    public function __construct(ProcessExecutor $processExecutor, IoService $ioService)
    {
        $this->ioService = $ioService;
        $this->processExecutor = $processExecutor;
    }

    /**
     * @param string $repo
     * @param string $branch
     * @param string $destination
     */
    public function checkout($repo, $branch, $destination)
    {
        $this->ioService->writeln("<info>Checkout out $repo to $destination</info>");

        $repo        = escapeshellarg($repo);
        $branch      = escapeshellarg($branch);
        $destination = escapeshellarg($destination);

        $this->processExecutor->execute(
            "git clone --progress -b {$branch} {$repo} {$destination}"
        );
    }
}
