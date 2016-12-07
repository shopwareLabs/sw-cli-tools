<?php

namespace Shopware\Install\Services;

use ShopwareCli\Services\IoService;

/**
 * creates the phpstorm vcs mapping file
 *
 * Class VcsGenerator
 * @package Shopware\Install\Services
 */
class VcsGenerator
{
    protected $templateVcsMapping = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<project version="4">
    <component name="VcsDirectoryMappings">
        %s
    </component>
</project>
EOF;

    protected $templateVcsMappingDirectory = '<mapping directory="$PROJECT_DIR$%s" vcs="Git" />';
    /**
     * @var \ShopwareCli\Services\IoService
     */
    private $ioService;

    public function __construct(IoService $ioService)
    {
        $this->ioService = $ioService;
    }

    public function createVcsMapping($installDir, $paths)
    {
        $this->ioService->writeln("<info>Generating VCS mapping</info>");

        $dir = $installDir.'/.idea';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $mappings = [];
        foreach ($paths as $path) {
            if ($path == '/') {
                $path = '';
            }
            $mappings[] = sprintf($this->templateVcsMappingDirectory, $path);
        }

        $mappings = implode("\n", $mappings);

        file_put_contents($dir . '/vcs.xml', sprintf($this->templateVcsMapping, $mappings));
    }
}
