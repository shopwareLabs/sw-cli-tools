<?php

namespace Shopware\Install\Services;


use ShopwareCli\Services\ProcessExecutor;

class PostInstall
{
    /**
     * @var \ShopwareCli\Services\ProcessExecutor
     */
    private $processExecutor;

    public function __construct(ProcessExecutor $processExecutor)
    {
        $this->processExecutor = $processExecutor;
    }

    public function fixPermissions($directory)
    {
        $command = sprintf('chmod 0777 -R "%s"', $directory . '/logs');
        $this->processExecutor->execute($command);

        $command = sprintf('chmod 0777 -R "%s"', $directory . '/cache');
        $this->processExecutor->execute($command);
    }
}