<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\GitUtil;
use ShopwareCli\Services\IoService;

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
     * @var GitUtil
     */
    private $gitUtil;

    /**
     * @param GitUtil   $gitUtil
     * @param IoService $ioService
     */
    public function __construct(GitUtil $gitUtil, IoService $ioService)
    {
        $this->ioService = $ioService;
        $this->gitUtil = $gitUtil;
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

        $this->gitUtil->run(
            "clone --progress -b {$branch} {$repo} {$destination}"
        );
    }
}
